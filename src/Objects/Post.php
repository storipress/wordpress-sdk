<?php

declare(strict_types=1);

namespace Storipress\WordPress\Objects;

use stdClass;

class Post extends WordPressObject
{
    public int $id;

    public string $date;

    public string $date_gmt;

    public RenderObject $guid;

    public string $modified;

    public string $modified_gmt;

    public ?string $password;

    public string $slug;

    public string $status;

    public string $type;

    public string $link;

    public RenderObject $title;

    public RenderObject $content;

    public RenderObject $excerpt;

    public int $author;

    public int $featured_media;

    public string $comment_status;

    public string $ping_status;

    public bool $sticky;

    public string $template;

    public string $format;

    public stdClass $meta;

    /**
     * @var int[]
     */
    public array $categories;

    /**
     * @var int[]
     */
    public array $tags;

    public static function from(stdClass $data): static
    {
        $data->guid = RenderObject::from($data->guid);

        $data->title = RenderObject::from($data->title);

        $data->content = RenderObject::from($data->content);

        $data->excerpt = RenderObject::from($data->excerpt);

        return parent::from($data);
    }
}
