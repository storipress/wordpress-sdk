<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use Illuminate\Http\Client\PendingRequest;
use stdClass;
use Storipress\WordPress\Exceptions\WordPressException;

class GeneralRequest extends Request
{
    /**
     * @param  non-empty-string  $path
     * @param  array<mixed>  $arguments
     * @return stdClass|array<int, stdClass>
     *
     * @throws WordPressException
     */
    public function get(string $path, array $arguments = []): stdClass|array
    {
        return $this->request('get', $path, $arguments);
    }

    /**
     * @param  non-empty-string  $path
     * @param  array<mixed>  $arguments
     * @return stdClass|array<int, stdClass>
     *
     * @throws WordPressException
     */
    public function post(string $path, array $arguments = []): stdClass|array
    {
        return $this->request('post', $path, $arguments);
    }

    /**
     * @param  non-empty-string  $path
     * @param  array<mixed>  $arguments
     * @return stdClass|array<int, stdClass>
     *
     * @throws WordPressException
     */
    public function patch(string $path, array $arguments = []): stdClass|array
    {
        return $this->request('patch', $path, $arguments);
    }

    /**
     * @param  non-empty-string  $path
     * @param  array<mixed>  $arguments
     *
     * @throws WordPressException
     */
    public function delete(string $path, array $arguments = []): bool
    {
        return $this->request('delete', $path, $arguments);
    }

    public function getUrl(string $path, PendingRequest $http): string
    {
        if ($this->app->isPrettyUrl()) {
            return sprintf(
                '/%s/%s',
                trim($this->app->prefix(), '/'),
                ltrim($path, '/'),
            );
        }

        $http->withQueryParameters([
            'rest_route' => sprintf('/%s', ltrim($path, '/')),
        ]);

        return '/';
    }
}
