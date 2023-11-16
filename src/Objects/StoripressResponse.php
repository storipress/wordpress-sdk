<?php

namespace Storipress\WordPress\Objects;

class StoripressResponse extends WordPressObject
{
    public bool $success;

    public ?string $message;

    public ?int $code;
}
