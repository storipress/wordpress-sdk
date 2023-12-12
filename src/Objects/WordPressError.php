<?php

declare(strict_types=1);

namespace Storipress\WordPress\Objects;

use stdClass;

class WordPressError extends WordPressObject
{
    public string $code;

    public string $message;

    public ?stdClass $data;
}
