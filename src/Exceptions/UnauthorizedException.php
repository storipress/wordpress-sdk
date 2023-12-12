<?php

declare(strict_types=1);

namespace Storipress\WordPress\Exceptions;

use Storipress\WordPress\Objects\ErrorException;

class UnauthorizedException extends WordPressException
{
    public function __construct(ErrorException $error)
    {
        parent::__construct($error, 401);
    }
}
