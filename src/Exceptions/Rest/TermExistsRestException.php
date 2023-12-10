<?php

declare(strict_types=1);

namespace Storipress\WordPress\Exceptions\Rest;

use Storipress\WordPress\Objects\ErrorException;
use Throwable;

class TermExistsRestException extends RestHttpException
{
    /**
     * Duplicate term id
     */
    public mixed $term_id;

    public function __construct(ErrorException $error, int $code = 0, ?Throwable $previous = null)
    {
        $this->term_id = data_get($error->data, 'term_id');

        parent::__construct($error, $code, $previous);
    }

    public function getTermId(): mixed
    {
        return $this->term_id;
    }
}
