<?php

namespace App\Http\Controllers\Next;

use App\Http\Controllers\Controller;
use App\Jobs\AddContactsToCampaignJob;
use App\Jobs\AddSendingsToBatchJob;
use App\Models\Campaign;
use App\Models\Directory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $campaigns = Campaign::query()
            ->with('directory')
            ->orderBy('created_at', 'DESC');

        switch ($request->filter) {
                // case 'starred':
                // $campaigns = $campaigns->where('status', Campaign::STATUS_DRAFT);
                // break;
            case 'draft':
                $campaigns = $campaigns->where('status', Campaign::STATUS_DRAFT);

                break;
                // case 'programmed':
                // $campaigns = $campaigns->where('status', Campaign::STATUS_DRAFT);
                // break;
            case 'ready':
                $campaigns = $campaigns->where('status', Campaign::STATUS_READY);

                break;
            case 'in-progress':
                $campaigns = $campaigns->where('status', Campaign::STATUS_IN_PROGRESS);

                break;
            case 'finished':
                $campaigns = $campaigns->where('status', Campaign::STATUS_FINISHED);

                break;
        }

        $campaigns = $campaigns->paginate(15);

        $data = $campaigns->getCollection();
        $data->each(function ($item) {
            $item->sent = $item->contacts()->wherePivot('sent_at', '!=', null)->count();
            $item->total = $item->contacts()->count();
            $item->percent = null;

            if ($item->total != 0) {
                $item->percent = round($item->sent / $item->total * 100);
            }
        });
        $campaigns->setCollection($data);

        return inertia('campaigns/index', [
            'campaigns' => $campaigns,
            'filter' => $request->filter,
        ]);
    }

    public function create()
    {
        $directories = Directory::restricted()->pluck('name', 'id');

        return inertia('campaigns/create', compact('directories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string'],
            'directory_id' => ['required'],
        ]);

        $campaign = new Campaign();
        $campaign->sender_name = $request->sender_name;
        $campaign->message = $request->message;
        $campaign->directory_id = $request->directory_id;
        $campaign->status = Campaign::STATUS_DRAFT;
        $campaign->save();

        return redirect()->route('next.campaigns.show', $campaign->id);
    }

    public function show($id)
    {
        $campaign = Campaign::query()
            ->with('directory')
            ->findOrFail($id);

        $campaign->sent = $campaign->contacts()->wherePivot('sent_at', '!=', null)->count();
        $campaign->total = $campaign->contacts()->count();
        $campaign->percent = 0;

        $campaign->directory_total = $campaign->directory->contacts_count;
        $campaign->directory_percent = 0;

        if ($campaign->total != 0) {
            $campaign->percent = round($campaign->sent / $campaign->total * 100);
        }

        if ($campaign->directory_total != 0) {
            $campaign->directory_percent = round($campaign->total / $campaign->directory_total * 100);
        }

        return inertia('campaigns/show', compact('campaign'));
    }

    public function edit($id)
    {
        $campaign = Campaign::findOrFail($id);
        $directories = Directory::restricted()->pluck('name', 'id');

        return inertia('campaigns/edit', compact('campaign', 'directories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'message' => ['required', 'string'],
            'directory_id' => ['required'],
        ]);

        $campaign = Campaign::findOrFail($id);
        $campaign->sender_name = $request->sender_name;
        $campaign->message = $request->message;
        $campaign->directory_id = $request->directory_id;
        $campaign->save();

        return redirect()->route('next.campaigns.show', $campaign->id);
    }

    public function destroy($id)
    {
        $campaign = Campaign::findOrFail($id);

        DB::table('campaign_contact')
            ->where('campaign_id', $campaign->id)
            ->delete();

        $campaign->delete();

        return redirect()->route('next.campaigns.index');
    }

    public function prepare($id)
    {
        $campaign = Campaign::findOrFail($id);
        if ($campaign->status != Campaign::STATUS_DRAFT) {
            // TODO: Error
            return;
        }

        $campaign->status = 'preparing';
        $campaign->save();

        Bus::chain([
            new AddContactsToCampaignJob($campaign),
            function () use ($campaign) {
                $campaign->status = Campaign::STATUS_READY;
                $campaign->save();
            },
        ])->onQueue('prepare')->dispatch();

        return redirect()->route('next.campaigns.show', $campaign->id);
    }

    public function send($id)
    {
        $campaign = Campaign::findOrFail($id);
        if ($campaign->status != Campaign::STATUS_READY && $campaign->status != Campaign::STATUS_FINISHED) {
            // TODO: Error
            return;
        }

        $campaign->status = Campaign::STATUS_IN_PROGRESS;
        $campaign->save();

        Bus::chain([
            new AddSendingsToBatchJob($campaign),
        ])->onQueue('prepare')->dispatch();

        return redirect()->route('next.campaigns.show', $campaign->id);
    }
}
