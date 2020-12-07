<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;

class HomeController extends Controller
{
    public function index()
    {
        return inertia('home');
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
                'token' => $socialite_user->token,
                'refresh_token' => $socialite_user->refreshToken,
                'expires_in' => $socialite_user->expiresIn,
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
