<?php

declare(strict_types=1);

namespace Storipress\WordPress\Exceptions\Rest;

use Storipress\WordPress\Exceptions\HttpException as BaseHttpException;
use Storipress\WordPress\Objects\ErrorException;
use Throwable;

abstract class HttpException extends BaseHttpException
{
    public string $raw_message;

    public string $message_code;

    public function __construct(ErrorException $error, int $code = 0, ?Throwable $previous = null)
    {
        $this->raw_message = $error->raw_message;

        $this->message_code = $error->code;

        parent::__construct($error->message, $code, $previous);
    }

    public function getRawMessage(): string
    {
        return $this->raw_message;
    }

    public function getMessageCode(): string
    {
        return $this->message_code;
    }
}
