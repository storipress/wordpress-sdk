<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use Storipress\WordPress\Exceptions\WordPressException;
use Storipress\WordPress\Objects\Category as CategoryObject;

class Category extends Request
{
    /**
     * https://developer.wordpress.org/rest-api/reference/categories/#list-categories
     *
     * @return array<int, CategoryObject>
     *
     * @throws WordPressException
     */
    public function list(): array
    {
        $data = $this->request('get', '/categories');

        if (!is_array($data)) {
            throw $this->unexpectedValueException();
        }

        return array_map(
            fn ($data) => CategoryObject::from($data),
            $data
        );
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/categories/#create-a-category
     *
     * @param  array<string, mixed>  $arguments
     *
     * @throws WordPressException
     */
    public function create(array $arguments): CategoryObject
    {
        $data = $this->request('post', '/categories', $arguments);

        if (is_array($data)) {
            throw $this->unexpectedValueException();
        }

        return CategoryObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/categories/#retrieve-a-category
     *
     * @throws WordPressException
     */
    public function retrieve(int $categoryId, string $context = 'view'): CategoryObject
    {
        $uri = sprintf('/categories/%d', $categoryId);

        $data = $this->request('get', $uri, [
            'context' => $context,
        ]);

        if (is_array($data)) {
            throw $this->unexpectedValueException();
        }

        return CategoryObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/categories/#update-a-category
     *
     * @param  array<string, mixed>  $arguments
     *
     * @throws WordPressException
     */
    public function update(int $categoryId, array $arguments): CategoryObject
    {
        $uri = sprintf('/categories/%d', $categoryId);

        $data = $this->request('patch', $uri, $arguments);

        if (is_array($data)) {
            throw $this->unexpectedValueException();
        }

        return CategoryObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/categories/#delete-a-category
     *
     * @throws WordPressException
     */
    public function delete(int $categoryId): bool
    {
        $uri = sprintf('/categories/%d', $categoryId);

        return $this->request('delete', $uri, [
            'force' => true,
        ]);
    }
}
