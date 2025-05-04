<?php

namespace App\Providers;

use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Illuminate\Auth\GenericUser;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    public function register()
    {
        $this->app->singleton(Auth0::class, function () {
            $config = array_merge(config('auth0'), [
                'strategy' => 'webapp',
            ]);

            $configuration = new SdkConfiguration($config);

            return new Auth0($configuration);
        });
    }
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Auth::viaRequest('auth0', function () {
            /**@var Auth0 $auth0*/
            $auth0 = app(Auth0::class);
            $credentials = $auth0->getCredentials();

            if (empty($credentials)) {
                return null;
            }

            return new GenericUser($credentials->user);
        });
    }
}
