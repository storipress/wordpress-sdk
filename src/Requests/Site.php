<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use Storipress\WordPress\Exceptions\WordPressException;
use Storipress\WordPress\Objects\Site as SiteObject;

class Site extends Request
{
    /**
     * https://developer.wordpress.org/rest-api/reference/settings/#retrieve-a-site-setting
     *
     *
     * @throws WordPressException
     */
    public function retrieve(): SiteObject
    {
        $data = $this->request('get', '/settings');

        return SiteObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/settings/#update-a-site-setting
     *
     * @param array{
     *     title?: string,
     *     description?: string,
     *     url?: string,
     *     email?: string,
     *     timezone?: string,
     *     date_format?: string,
     *     time_format?: string,
     *     start_of_week?: int,
     *     language?: string,
     *     use_smilies?: bool,
     *     default_category?: int,
     *     default_post_format?: string,
     *     posts_per_page?: int,
     *     show_on_front?: string,
     *     page_on_front?: int,
     *     page_for_posts?: int,
     *     default_ping_status?: string,
     *     default_comment_status?: string,
     *     site_logo?: int|null,
     *     site_icon?: int,
     * } $arguments
     *
     * @throws WordPressException
     */
    public function update(array $arguments): SiteObject
    {
        $data = $this->request('post', '/settings', $arguments);

        return SiteObject::from($data);
    }
}
