<?php

namespace App\Providers;

use App\Console\Commands\PublishSiteMediaCommand;
use App\Http\Controllers\Web\AssistAuthController;
use App\Services\AssistInstallerService;
use App\Services\SiteContentService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AssistServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PublishSiteMediaCommand::class,
            ]);
        }

        $installer = $this->app->make(AssistInstallerService::class);

        if (! $installer->isInstalled()) {
            config(['session.driver' => 'file']);
        }

        View::composer('layouts.assist', function ($view) {
            if (! isset($view->getData()['seo'])) {
                $slug = match (request()->route()?->getName()) {
                    'assist.home' => 'home',
                    'assist.pricing' => 'pricing',
                    'assist.docs' => 'docs',
                    'assist.privacy' => 'privacy',
                    'assist.terms' => 'terms',
                    'login', 'assist.login' => 'login',
                    'assist.register' => 'register',
                    'assist.password.request' => 'forgot_password',
                    'assist.password.reset' => 'reset_password',
                    default => 'home',
                };
                $view->with('seo', app(SiteContentService::class)->forSlug($slug)['seo']);
            }
        });

        $this->app->booted(function () {
            if (Route::has('login') && ! Route::has('assist.login')) {
                Route::middleware('guest')
                    ->get('/login', [AssistAuthController::class, 'showLogin'])
                    ->name('assist.login');
            }
        });
    }
}
