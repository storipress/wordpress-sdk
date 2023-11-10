<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use Storipress\WordPress\Exceptions\HttpException;
use Storipress\WordPress\Exceptions\UnexpectedValueException;
use Storipress\WordPress\Objects\User as UserObject;

class User extends Request
{
    /**
     * https://developer.wordpress.org/rest-api/reference/users/#list-users
     *
     * @return UserObject[]
     *
     * @throws HttpException
     * @throws UnexpectedValueException
     */
    public function list(): array
    {
        $data = $this->request('get', '/users');

        if (!is_array($data)) {
            throw new UnexpectedValueException();
        }

        return array_map(
            fn ($data) => UserObject::from($data),
            $data
        );
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/users/#create-a-user
     *
     * @param  array<string, mixed>  $arguments
     *
     * @throws HttpException
     * @throws UnexpectedValueException
     */
    public function create(array $arguments): UserObject
    {
        $data = $this->request('post', '/users', $arguments);

        if (is_array($data)) {
            throw new UnexpectedValueException();
        }

        return UserObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/users/#retrieve-a-user
     *
     * @throws HttpException
     * @throws UnexpectedValueException
     */
    public function retrieve(int $userId): UserObject
    {
        $uri = sprintf('/users/%d', $userId);

        $data = $this->request('get', $uri);

        if (is_array($data)) {
            throw new UnexpectedValueException();
        }

        return UserObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/users/#update-a-user
     *
     * @param  array<string, mixed>  $arguments
     *
     * @throws HttpException
     * @throws UnexpectedValueException
     */
    public function update(int $userId, array $arguments): UserObject
    {
        $uri = sprintf('/users/%d', $userId);

        $data = $this->request('patch', $uri, $arguments);

        if (is_array($data)) {
            throw new UnexpectedValueException();
        }

        return UserObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/users/#delete-a-user
     *
     * @throws HttpException
     * @throws UnexpectedValueException
     */
    public function delete(int $userId): bool
    {
        $uri = sprintf('/users/%d', $userId);

        return $this->request('delete', $uri);
    }
}
