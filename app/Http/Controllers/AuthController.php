<?php

namespace App\Http\Controllers;

use Auth0\SDK\Auth0;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

class AuthController
{
    public function login(Auth0 $auth0): Redirector|RedirectResponse|Application
    {
        return redirect($auth0->login(route('callback')));
    }

    public function logout(Auth0 $auth0): Redirector|RedirectResponse|Application
    {
        return redirect($auth0->logout(route('home')));
    }

    public function signup(Auth0 $auth0): Redirector|RedirectResponse|Application
    {
        return redirect($auth0->signup(route('callback')));
    }

    public function callback(Auth0 $auth0): Redirector|RedirectResponse|Application
    {
        try {

            $auth0->exchange(route('profile'));
        } catch (\Exception $ex) {
            logger($ex->getMessage(), [$ex]);
            $auth0->clear();
        }

        return redirect(route('profile'));
    }
}
