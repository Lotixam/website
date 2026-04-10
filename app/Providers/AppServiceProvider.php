<?php

namespace App\Providers;

use App\Models\WorkflowTemplate;
use App\Policies\WorkflowTemplatePolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $tmpDir = $this->app->storagePath('framework/tmp');
        if (! is_dir($tmpDir)) {
            @mkdir($tmpDir, 0755, true);
        }
        if (is_dir($tmpDir) && is_writable($tmpDir)) {
            putenv('TMPDIR='.$tmpDir);
            $_ENV['TMPDIR'] = $tmpDir;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(WorkflowTemplate::class, WorkflowTemplatePolicy::class);

        RateLimiter::for('api-login', function (Request $request) {
            $key = sprintf('%s|%s', $request->ip(), (string) $request->input('username', ''));

            return Limit::perMinute(5)->by($key);
        });

        if ($this->app->environment('local')) {
            URL::forceScheme('http');
        } elseif ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
