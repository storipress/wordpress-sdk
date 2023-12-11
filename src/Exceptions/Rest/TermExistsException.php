<?php

declare(strict_types=1);

namespace Storipress\WordPress\Exceptions\Rest;

use Storipress\WordPress\Objects\ErrorException;
use Throwable;

class TermExistsException extends HttpException
{
    /**
     * Duplicate term id
     */
    public ?int $term_id;

    public function __construct(ErrorException $error, int $code = 0, ?Throwable $previous = null)
    {
        $this->term_id = $error->data['term_id'] ?? null;

        parent::__construct($error, $code, $previous);
    }

    public function getTermId(): mixed
    {
        return $this->term_id;
    }
}
