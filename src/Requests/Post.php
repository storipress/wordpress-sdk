<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use Storipress\WordPress\Exceptions\WordPressException;
use Storipress\WordPress\Objects\Post as PostObject;

class Post extends Request
{
    /**
     * https://developer.wordpress.org/rest-api/reference/posts/#list-posts
     *
     * @param  array<string, mixed>  $arguments
     * @return array<int, PostObject>
     *
     * @throws WordPressException
     */
    public function list(array $arguments = []): array
    {
        $data = $this->request(
            'get',
            '/posts',
            $arguments,
            true,
        );

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
     * @throws WordPressException
     */
    public function create(array $arguments): PostObject
    {
        $data = $this->request('post', '/posts', $arguments);

        return PostObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/posts/#retrieve-a-post
     *
     * @throws WordPressException
     */
    public function retrieve(int $postId, string $context = 'view'): PostObject
    {
        $uri = sprintf('/posts/%d', $postId);

        $data = $this->request('get', $uri, [
            'context' => $context,
        ]);

        return PostObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/posts/#update-a-post
     *
     * @param  array<string, mixed>  $arguments
     *
     * @throws WordPressException
     */
    public function update(int $postId, array $arguments): PostObject
    {
        $uri = sprintf('/posts/%d', $postId);

        $data = $this->request('post', $uri, $arguments);

        return PostObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/posts/#delete-a-post
     *
     * @throws WordPressException
     */
    public function delete(int $postId, bool $force = false): bool
    {
        $uri = sprintf('/posts/%s', $postId);

        return $this->request('delete', $uri, [
            'force' => $force,
        ]);
    }
}
