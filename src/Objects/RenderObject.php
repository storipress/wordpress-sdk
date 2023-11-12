<?php

namespace Storipress\WordPress\Objects;

class RenderObject extends WordPressObject
{
    public ?string $raw;

    public string $rendered;

    public ?bool $protected;
}
