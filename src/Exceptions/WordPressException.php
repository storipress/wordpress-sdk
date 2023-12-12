<?php

declare(strict_types=1);

namespace Storipress\WordPress\Exceptions;

use Exception as BaseException;
use Storipress\WordPress\Objects\WordPressError;

abstract class WordPressException extends BaseException
{
    /**
     * Duplicate term id
     */
    public ?int $term_id;

    public function __construct(public WordPressError $error, int $code)
    {
        $this->term_id = $error->data->term_id ?? null;

        parent::__construct($error->message, $code);
    }

    public function getTermId(): ?int
    {
        return $this->term_id;
    }
}
