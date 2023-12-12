<?php

declare(strict_types=1);

namespace Storipress\WordPress\Exceptions;

use Storipress\WordPress\Objects\ErrorException;

class UnexpectedValueException extends WordPressException
{
    public function __construct(ErrorException $error)
    {
        parent::__construct($error, 500);
    }
}
