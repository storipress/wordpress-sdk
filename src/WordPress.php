<?php

declare(strict_types=1);

namespace Storipress\WordPress;

use Illuminate\Http\Client\Factory;
use Storipress\WordPress\Requests\Category;
use Storipress\WordPress\Requests\GeneralRequest;
use Storipress\WordPress\Requests\Media;
use Storipress\WordPress\Requests\Post;
use Storipress\WordPress\Requests\PostRevision;
use Storipress\WordPress\Requests\Site;
use Storipress\WordPress\Requests\Tag;
use Storipress\WordPress\Requests\User;

class WordPress
{
    protected readonly GeneralRequest $request;

    protected readonly User $user;

    protected readonly Post $post;

    protected readonly PostRevision $postRevision;

    protected readonly Category $category;

    protected readonly Tag $tag;

    protected readonly Media $media;

    protected readonly Site $site;

    protected string $url;

    protected string $username;

    protected string $password;

    protected ?string $userAgent = null;

    protected string $prefix = 'wp-json';

    protected bool $prettyUrl = false;

    public function __construct(
        public Factory $http,
    ) {
        $this->request = new GeneralRequest($this);

        $this->user = new User($this);

        $this->post = new Post($this);

        $this->postRevision = new PostRevision($this);

        $this->category = new Category($this);

        $this->tag = new Tag($this);

        $this->media = new Media($this);

        $this->site = new Site($this);
    }

    public function instance(): static
    {
        return $this;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

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

    public function userAgent(): ?string
    {
        return $this->userAgent;
    }

    public function prefix(): string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function isPrettyUrl(): bool
    {
        return $this->prettyUrl;
    }

    public function prettyUrl(): static
    {
        $this->prettyUrl = true;

        return $this;
    }

    public function withUserAgent(string $userAgent): static
    {
        $this->userAgent = $userAgent;

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

    public function postRevision(): PostRevision
    {
        return $this->postRevision;
    }

    public function category(): Category
    {
        return $this->category;
    }

    public function tag(): Tag
    {
        return $this->tag;
    }

    public function media(): Media
    {
        return $this->media;
    }

    public function site(): Site
    {
        return $this->site;
    }
}
