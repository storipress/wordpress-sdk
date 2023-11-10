<?php

declare(strict_types=1);

namespace Storipress\WordPress\Objects;

class Category extends WordPressObject
{
    public int $id;

    public int $count;

    public ?string $description;

    public string $link;

    public string $name;

    public string $slug;

    public string $taxonomy;

    public int $parent;
}
