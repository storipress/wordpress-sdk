<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use Storipress\WordPress\Exceptions\WordPressException;
use Storipress\WordPress\Objects\Tag as TagObject;

class Tag extends Request
{
    /**
     * https://developer.wordpress.org/rest-api/reference/tags/#list-tags
     *
     * @param  array<string, mixed>  $arguments
     * @return array<int, TagObject>
     *
     * @throws WordPressException
     */
    public function list(array $arguments = []): array
    {
        $data = $this->request(
            'get',
            '/tags',
            $arguments,
            true,
        );

        return array_map(
            fn ($data) => TagObject::from($data),
            $data,
        );
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/tags/#create-a-tag
     *
     * @param  array<string, mixed>  $arguments
     *
     * @throws WordPressException
     */
    public function create(array $arguments): TagObject
    {
        $data = $this->request('post', '/tags', $arguments);

        return TagObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/tags/#retrieve-a-tag
     *
     * @throws WordPressException
     */
    public function retrieve(int $tagId, string $context = 'view'): TagObject
    {
        $uri = sprintf('/tags/%d', $tagId);

        $data = $this->request('get', $uri, [
            'context' => $context,
        ]);

        return TagObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/tags/#update-a-tag
     *
     * @param  array<string, mixed>  $arguments
     *
     * @throws WordPressException
     */
    public function update(int $tagId, array $arguments): TagObject
    {
        $uri = sprintf('/tags/%d', $tagId);

        $data = $this->request('post', $uri, $arguments);

        return TagObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/tags/#delete-a-tag
     *
     * @throws WordPressException
     */
    public function delete(int $tagId): bool
    {
        $uri = sprintf('/tags/%d', $tagId);

        return $this->request('delete', $uri, [
            'force' => true,
        ]);
    }
}
