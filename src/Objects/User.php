<?php

declare(strict_types=1);

namespace Storipress\WordPress\Objects;

use stdClass;

class User extends WordPressObject
{
    public int $id;

    public string $username;

    public string $name;

    public string $first_name;

    public string $last_name;

    public string $nickname;

    public string $slug;

    public string $email;

    public string $url;

    public ?string $description;

    public string $link;

    public string $registered_date;

    /**
     * @var array<int, 'subscriber'|'contributor'|'author'|'editor'|'administrator'>
     */
    public array $roles;

    public stdClass $avatar_urls;
}
