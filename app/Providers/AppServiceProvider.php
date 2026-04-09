<?php

namespace App\Providers;

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
        if ($this->app->environment('local')) {
            URL::forceScheme('http');
        }
    }
}
