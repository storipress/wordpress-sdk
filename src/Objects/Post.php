<?php

declare(strict_types=1);

namespace Storipress\WordPress\Objects;

use stdClass;

class Post extends WordPressObject
{
    public int $id;

    public string $date;

    public string $date_gmt;

    public Render $guid;

    public string $modified;

    public string $modified_gmt;

    public ?string $password;

    public string $slug;

    public string $status;

    public string $type;

    public string $link;

    public Render $title;

    public Render $content;

    public Render $excerpt;

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
        $data->guid = Render::from($data->guid);

        $data->title = Render::from($data->title);

        $data->content = Render::from($data->content);

        $data->excerpt = Render::from($data->excerpt);

        return parent::from($data);
    }
}
