<?php

declare(strict_types=1);

namespace Storipress\WordPress\Objects;

use stdClass;

abstract class WordPressObject
{
    /**
     * @var array<string, mixed>
     */
    private array $_map = [];

    final public function __construct(
        protected readonly stdClass $raw,
    ) {
        foreach (get_object_vars($this->raw) as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public static function from(stdClass $data): static
    {
        return new static($data);
    }

    public function getRaw(): stdClass
    {
        return $this->raw;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->_map[$key] = $value;
    }

    public function __get(string $key): mixed
    {
        return $this->_map[$key] ?? null;
    }
}
