<?php

declare(strict_types=1);

namespace Storipress\WordPress\Facades;

use Illuminate\Support\Facades\Facade;
use Storipress\WordPress\Requests\Category;
use Storipress\WordPress\Requests\GeneralRequest;
use Storipress\WordPress\Requests\Post;
use Storipress\WordPress\Requests\Tag;
use Storipress\WordPress\Requests\User;

/**
 * @method static Post post()
 * @method static Category category()
 * @method static Tag tag()
 * @method static User user()
 * @method static GeneralRequest request()
 * @method static \Storipress\WordPress\WordPress setSite(string $site)
 * @method static \Storipress\WordPress\WordPress site()
 * @method static \Storipress\WordPress\WordPress setUsername(string $username)
 * @method static \Storipress\WordPress\WordPress username()
 * @method static \Storipress\WordPress\WordPress setApplicationKey(string $key)
 * @method static \Storipress\WordPress\WordPress applicationKey()
 * @method static \Storipress\WordPress\WordPress instance()
 */
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
