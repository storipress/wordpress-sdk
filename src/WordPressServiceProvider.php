<?php

declare(strict_types=1);

namespace Storipress\WordPress;

use Illuminate\Support\ServiceProvider;

class WordPressServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            'wordpress',
            fn () => $this->app->make(WordPress::class),
        );
    }
}
