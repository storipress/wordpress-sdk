<?php

declare(strict_types=1);

namespace Storipress\WordPress\Objects;

use stdClass;

class PostRevision extends WordPressObject
{
    public int $author;

    public string $date;

    public string $date_gmt;

    public int $id;

    public string $modified;

    public string $modified_gmt;

    public int $parent;

    public string $slug;

    public Render $guid;

    public Render $title;

    public Render $content;

    public Render $excerpt;

    public stdClass $meta;

    public stdClass $_links;

    public static function from(stdClass $data): static
    {
        $data->guid = Render::from($data->guid);

        $data->title = Render::from($data->title);

        $data->content = Render::from($data->content);

        $data->excerpt = Render::from($data->excerpt);

        return parent::from($data);
    }
}
