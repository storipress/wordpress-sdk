<?php

declare(strict_types=1);

namespace Storipress\WordPress\Facades;

use Illuminate\Support\Facades\Facade;

class WordPress extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'wordpress';
    }
}
