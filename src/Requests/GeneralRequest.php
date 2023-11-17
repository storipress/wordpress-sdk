<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use stdClass;

class GeneralRequest extends Request
{
    /**
     * @param  non-empty-string  $path
     * @param  array<mixed>  $arguments
     * @return stdClass|array<int, stdClass>
     *
     * @throws \Storipress\WordPress\Exceptions\HttpException
     * @throws \Storipress\WordPress\Exceptions\UnexpectedValueException
     */
    public function get(string $path, array $arguments): stdClass|array
    {
        return $this->request('get', $path, $arguments);
    }

    /**
     * @param  non-empty-string  $path
     * @param  array<mixed>  $arguments
     * @return stdClass|array<int, stdClass>
     *
     * @throws \Storipress\WordPress\Exceptions\HttpException
     * @throws \Storipress\WordPress\Exceptions\UnexpectedValueException
     */
    public function post(string $path, array $arguments): stdClass|array
    {
        return $this->request('post', $path, $arguments);
    }

    /**
     * @param  non-empty-string  $path
     * @param  array<mixed>  $arguments
     * @return stdClass|array<int, stdClass>
     *
     * @throws \Storipress\WordPress\Exceptions\HttpException
     * @throws \Storipress\WordPress\Exceptions\UnexpectedValueException
     */
    public function patch(string $path, array $arguments): stdClass|array
    {
        return $this->request('patch', $path, $arguments);
    }

    /**
     * @param  non-empty-string  $path
     * @param  array<mixed>  $arguments
     *
     * @throws \Storipress\WordPress\Exceptions\HttpException
     * @throws \Storipress\WordPress\Exceptions\UnexpectedValueException
     */
    public function delete(string $path, array $arguments): bool
    {
        return $this->request('delete', $path, $arguments);
    }

    public function getUrl(string $path): string
    {
        return sprintf('%s/wp-json/%s',
            rtrim($this->app->site(), '/'),
            ltrim($path, '/')
        );
    }
}
