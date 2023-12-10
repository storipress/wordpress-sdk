<?php

namespace Storipress\WordPress\Objects;

use stdClass;

class ErrorException extends WordPressObject
{
    public string $code;

    public string $message;

    public string $raw_message;

    public stdClass $data;
}
