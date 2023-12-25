<?php

declare(strict_types=1);

namespace Storipress\WordPress\Objects;

use stdClass;

class Media extends WordPressObject
{
    public int $id;

    public string $date;

    public string $date_gmt;

    public Render $guid;

    public string $modified;

    public string $modified_gmt;

    public string $slug;

    public string $status;

    public string $type;

    public string $link;

    public Render $title;

    public int $author;

    public string $comment_status;

    public string $ping_status;

    public string $template;

    public Render $description;

    public Render $caption;

    public stdClass $meta;

    public string $alt_text;

    public string $media_type;

    public string $mime_type;

    public MediaDetails $media_details;

    public ?int $post;

    public string $source_url;

    public static function from(stdClass $data): static
    {
        $data->guid = Render::from($data->guid);

        $data->title = Render::from($data->title);

        $data->description = Render::from($data->description);

        $data->caption = Render::from($data->caption);

        $data->meta = is_array($data->meta) ? (object) $data->meta : $data->meta;

        $data->media_details = MediaDetails::from($data->media_details);

        return parent::from($data);
    }
}
