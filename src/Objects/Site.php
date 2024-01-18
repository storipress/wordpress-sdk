<?php

declare(strict_types=1);

namespace Storipress\WordPress\Objects;

class Site extends WordPressObject
{
    public string $title;

    public string $description;

    public string $url;

    public string $email;

    public string $timezone;

    public string $date_format;

    public string $time_format;

    public int $start_of_week;

    public string $language;

    public bool $use_smilies;

    public int $default_category;

    public string $default_post_format;

    public int $posts_per_page;

    public string $show_on_front;

    public int $page_on_front;

    public int $page_for_posts;

    public string $default_ping_status;

    public string $default_comment_status;

    public ?int $site_logo;

    public int $site_icon;
}
