<?php

namespace App\Http\Controllers;

use App\Models\Deployment;
use App\Models\Project;
use App\Models\Server;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Searchable\Search;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('Home');
    }

    public function settings()
    {
        $accounts = auth()
            ->user()
            ->social_accounts()
            ->get();

        return Inertia::render('Settings', [
            'hasGithubAccount' => $accounts->firstWhere('provider', 'github')->exists(),
        ]);
    }

    public function redirectToProvider()
    {
        return Socialite::driver('github')
            ->scopes(['read:user', 'repo'])
            ->redirect();
    }

    public function handleProviderCallback()
    {
        try {
            $socialite_user = Socialite::driver('github')->user();

            auth()->user()->social_accounts()->updateOrCreate([
                'provider' => 'github',
            ], [
                'provider_user_id' => $socialite_user->getId(),
                'provider_user_name' => $socialite_user->getNickname(),
                'token' => $socialite_user->token,
            ]);

            return redirect()
                ->route('home')
                ->with('success', 'GitHub account successfully linked');
        } catch (\Throwable $th) {
            return redirect()
                ->route('home')
                ->with('error', 'An error occured. ' . $th->getMessage());
        }
    }

    public function search(Request $request)
    {
        $searchResults = (new Search())
            ->registerModel(Project::class, ['name', 'repository'])
            ->registerModel(Server::class, ['name', 'ssh_host'])
            ->registerModel(Deployment::class, ['release'])
            ->search($request->q)
            ->groupByType();

        return response()->json($searchResults);
    }
}
