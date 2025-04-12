<?php

namespace App\Providers;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use App\Mail\Transport\MailtrapTransport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Interfaces\TaskRepositoryInterface::class,
            \App\Repositories\TaskRepository::class,
            \App\Services\TaskService::class
        );    
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(MailManager $mailManager): void
    {
        $mailManager->extend('mailtrap', function () {
            return new MailtrapTransport(env('MAIL_MAILTRAP_API_TOKEN'));
        });
    }
}
