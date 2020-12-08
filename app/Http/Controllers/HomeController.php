<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;

class HomeController extends Controller
{
    public function index()
    {
        $github_account = auth()
            ->user()
            ->social_accounts()
            ->where('provider', 'github')
            ->first();

        return inertia('home', compact('github_account'));
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
                ->with('error', 'An error occured');
        }
    }
}
