<?php

namespace Storipress\WordPress;

use Illuminate\Http\Client\Factory;
use Storipress\WordPress\Requests\Category;
use Storipress\WordPress\Requests\Post;
use Storipress\WordPress\Requests\Tag;
use Storipress\WordPress\Requests\User;

class WordPress
{
    protected readonly Post $post;

    protected readonly Tag $tag;

    protected readonly Category $category;

    protected readonly User $user;

    protected string $site;

    protected string $username;

    protected string $applicationKey;

    public function __construct(
        public Factory $http,
    ) {
        $this->post = new Post($this);

        $this->tag = new Tag($this);

        $this->category = new Category($this);

        $this->user = new User($this);
    }

    public function instance(): static
    {
        return $this;
    }

    public function setApplicationKey(string $key): static
    {
        $this->applicationKey = $key;

        return $this;
    }

    public function applicationKey(): string
    {
        return $this->applicationKey;
    }

    public function setSite(string $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function site(): string
    {
        return $this->site;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function post(): Post
    {
        return $this->post;
    }

    public function category(): Category
    {
        return $this->category;
    }

    public function tag(): Tag
    {
        return $this->tag;
    }

    public function user(): User
    {
        return $this->user;
    }
}
