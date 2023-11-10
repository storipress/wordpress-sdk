<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use Storipress\WordPress\Exceptions\HttpException;
use Storipress\WordPress\Exceptions\UnexpectedValueException;
use Storipress\WordPress\Objects\Post as PostObject;

class Post extends Request
{
    /**
     * https://developer.wordpress.org/rest-api/reference/posts/#list-posts
     *
     * @return PostObject[]
     *
     * @throws HttpException
     * @throws UnexpectedValueException
     */
    public function list(): array
    {
        $data = $this->request('get', '/posts');

        if (!is_array($data)) {
            throw new UnexpectedValueException();
        }

        return array_map(
            fn ($data) => PostObject::from($data),
            $data,
        );
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/posts/#create-a-post
     *
     * @param  array<string, mixed>  $arguments
     *
     * @throws HttpException
     * @throws UnexpectedValueException
     */
    public function create(array $arguments): PostObject
    {
        $data = $this->request('post', '/posts', $arguments);

        if (is_array($data)) {
            throw new UnexpectedValueException();
        }

        return PostObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/posts/#retrieve-a-post
     *
     * @throws HttpException
     * @throws UnexpectedValueException
     */
    public function retrieve(int $postId): PostObject
    {
        $uri = sprintf('/posts/%d', $postId);

        $data = $this->request('get', $uri);

        if (is_array($data)) {
            throw new UnexpectedValueException();
        }

        return PostObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/posts/#update-a-post
     *
     * @param  array<string, mixed>  $arguments
     *
     * @throws HttpException
     * @throws UnexpectedValueException
     */
    public function update(int $postId, array $arguments): PostObject
    {
        $uri = sprintf('/posts/%d', $postId);

        $data = $this->request('patch', $uri, $arguments);

        if (is_array($data)) {
            throw new UnexpectedValueException();
        }

        return PostObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/posts/#update-a-post
     *
     * @throws HttpException
     * @throws UnexpectedValueException
     */
    public function delete(int $postId): bool
    {
        $uri = sprintf('/posts/%s', $postId);

        return $this->request('delete', $uri);
    }
}
