<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OAuthController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {

        $socialUser = Socialite::driver($provider)->user();

        $user = User::where('provider_id', $socialUser->id)->first();

        if (!$user) {

            $user = User::create([
                'name' => $socialUser->name ?? $socialUser->nickname,
                'email' => $socialUser->email ?? $socialUser->id.'@'.$provider.'.com',
                'provider' => $provider,
                'provider_id' => $socialUser->id,
                'password' => bcrypt('password')
            ]);
        }

        Auth::login($user);

        return redirect('/dashboard');
    }
}
