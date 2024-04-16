<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use Storipress\WordPress\Exceptions\WordPressException;
use Storipress\WordPress\Objects\PostRevision as PostRevisionObject;

class PostRevision extends Request
{
    /**
     * https://developer.wordpress.org/rest-api/reference/post-revisions/#list-post-revisions
     *
     * @param  array<string, mixed>  $arguments
     * @return array<int, PostRevisionObject>
     *
     * @throws WordPressException
     */
    public function list(int $postId, array $arguments = []): array
    {
        $uri = sprintf('/posts/%d/revisions', $postId);

        $data = $this->request(
            'get',
            $uri,
            $arguments,
            true,
        );

        return array_map(
            fn ($data) => PostRevisionObject::from($data),
            $data,
        );
    }
}
