<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use Storipress\WordPress\Exceptions\WordPressException;
use Storipress\WordPress\Objects\User as UserObject;

class User extends Request
{
    /**
     * https://developer.wordpress.org/rest-api/reference/users/#list-users
     *
     * @param  array<string, mixed>  $arguments
     * @return array<int, UserObject>
     *
     * @throws WordPressException
     */
    public function list(array $arguments = []): array
    {
        $data = $this->request(
            'get',
            '/users',
            $arguments,
            true,
        );

        return array_map(
            fn ($data) => UserObject::from($data),
            $data,
        );
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/users/#create-a-user
     *
     * @param  array<string, mixed>  $arguments
     *
     * @throws WordPressException
     */
    public function create(array $arguments): UserObject
    {
        $data = $this->request('post', '/users', $arguments);

        return UserObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/users/#retrieve-a-user
     *
     * @throws WordPressException
     */
    public function retrieve(int $userId, string $context = 'view'): UserObject
    {
        $uri = sprintf('/users/%d', $userId);

        $data = $this->request('get', $uri, [
            'context' => $context,
        ]);

        return UserObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/users/#update-a-user
     *
     * @param  array<string, mixed>  $arguments
     *
     * @throws WordPressException
     */
    public function update(int $userId, array $arguments): UserObject
    {
        $uri = sprintf('/users/%d', $userId);

        $data = $this->request('post', $uri, $arguments);

        return UserObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/users/#delete-a-user
     *
     * @throws WordPressException
     */
    public function delete(int $userId, int $reassign): bool
    {
        $uri = sprintf('/users/%d', $userId);

        return $this->request('delete', $uri, [
            'force' => true,
            'reassign' => $reassign,
        ]);
    }
}
