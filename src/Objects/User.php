<?php

declare(strict_types=1);

namespace Storipress\WordPress\Objects;

use stdClass;

class User extends WordPressObject
{
    public int $id;

    public string $name;

    public string $url;

    public ?string $description;

    public string $link;

    public string $slug;

    public stdClass $avatar_urls;
}
