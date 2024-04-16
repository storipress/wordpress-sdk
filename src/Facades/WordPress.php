<?php

declare(strict_types=1);

namespace Storipress\WordPress\Facades;

use Illuminate\Support\Facades\Facade;
use Storipress\WordPress\Requests\Category;
use Storipress\WordPress\Requests\GeneralRequest;
use Storipress\WordPress\Requests\Media;
use Storipress\WordPress\Requests\Post;
use Storipress\WordPress\Requests\PostRevision;
use Storipress\WordPress\Requests\Site;
use Storipress\WordPress\Requests\Tag;
use Storipress\WordPress\Requests\User;

/**
 * @method static GeneralRequest request()
 * @method static User user()
 * @method static Post post()
 * @method static PostRevision postRevision()
 * @method static Category category()
 * @method static Tag tag()
 * @method static Media media()
 * @method static Site site()
 * @method static \Storipress\WordPress\WordPress instance()
 * @method static string url()
 * @method static \Storipress\WordPress\WordPress setUrl(string $url)
 * @method static string username()
 * @method static \Storipress\WordPress\WordPress setUsername(string $username)
 * @method static string password()
 * @method static \Storipress\WordPress\WordPress setPassword(string $password)
 * @method static string|null userAgent()
 * @method static \Storipress\WordPress\WordPress withUserAgent(string $userAgent)
 * @method static string prefix()
 * @method static \Storipress\WordPress\WordPress setPrefix(string $prefix)
 * @method static \Storipress\WordPress\WordPress prettyUrl()
 * @method static bool isPrettyUrl()
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
