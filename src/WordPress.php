<?php

declare(strict_types=1);

namespace Storipress\WordPress;

use Illuminate\Http\Client\Factory;
use Storipress\WordPress\Requests\Category;
use Storipress\WordPress\Requests\GeneralRequest;
use Storipress\WordPress\Requests\Post;
use Storipress\WordPress\Requests\Tag;
use Storipress\WordPress\Requests\User;

class WordPress
{
    protected readonly GeneralRequest $request;

    protected readonly User $user;

    protected readonly Post $post;

    protected readonly Category $category;

    protected readonly Tag $tag;

    protected string $site;

    protected string $username;

    protected string $password;

    protected string $userAgent = 'Storipress/WordPress/2023-12-01';

    public function __construct(
        public Factory $http,
    ) {
        $this->request = new GeneralRequest($this);

        $this->user = new User($this);

        $this->post = new Post($this);

        $this->category = new Category($this);

        $this->tag = new Tag($this);
    }

    public function instance(): static
    {
        return $this;
    }

    public function site(): string
    {
        return $this->site;
    }

    public function setSite(string $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function setPassword(string $key): static
    {
        $this->password = $key;

        return $this;
    }

    public function request(): GeneralRequest
    {
        return $this->request;
    }

    public function user(): User
    {
        return $this->user;
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

    public function userAgent(): string
    {
        return $this->userAgent;
    }

    public function withUserAgent(string $userAgent): static
    {
        $this->userAgent = $userAgent;

        return $this;
    }
}
