<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\MailManager;
use App\Mail\Transport\MailtrapTransport;

class CustomMailServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('mailer', function ($app) {
            $manager = new MailManager($app);

            // Add your custom transport here
            $manager->extend('mailtrap', function () {
                $apiToken = env('MAILTRAP_API_TOKEN'); // Make sure the token is in your .env
                return new MailtrapTransport($apiToken);
            });

            return $manager;
        });
    }

    public function boot()
    {
        //
    }
}
