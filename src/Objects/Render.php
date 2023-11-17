<?php

declare(strict_types=1);

namespace Storipress\WordPress\Objects;

class Render extends WordPressObject
{
    public ?string $raw;

    public string $rendered;

    public ?bool $protected;
}
