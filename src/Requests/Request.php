<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use stdClass;
use Storipress\WordPress\Exceptions\HttpException;
use Storipress\WordPress\Exceptions\HttpUnknownError;
use Storipress\WordPress\Exceptions\NotFoundException;
use Storipress\WordPress\Exceptions\UnexpectedValueException;
use Storipress\WordPress\WordPress;

abstract class Request
{
    const VERSION = 'v2';

    public function __construct(
        protected readonly WordPress $app,
    ) {
        //
    }

    /**
     * @param  'get'|'post'|'patch'|'delete'  $method
     * @param  non-empty-string  $path
     * @param  array<mixed>  $options
     * @return ($method is 'delete' ? bool : stdClass|array<int, stdClass>)
     *
     * @throws UnexpectedValueException
     * @throws HttpException
     */
    protected function request(
        string $method,
        string $path,
        array $options = [],
    ): stdClass|array|bool {
        $http = $this->app->http
            ->withoutVerifying()
            ->withoutRedirecting()
            ->withBasicAuth($this->app->username(), $this->app->password());

        if ($this->app->userAgent()) {
            $http->withUserAgent($this->app->userAgent());
        }

        $response = $http->{$method}(
            $this->getUrl(
                $path,
                $this->app->prefix(),
                $this->app->isPrettyUrl()
            ),
            $options
        );

        if (!$response->successful()) {
            $this->error(
                $response->body(),
                $response->status(),
                $response->headers(),
            );
        }

        if ($method === 'delete') {
            return $response->successful();
        }

        $data = $response->object();

        if (!($data instanceof stdClass) && !is_array($data)) {
            throw new UnexpectedValueException();
        }

        return $data;
    }

    public function getUrl(string $path, string $basePath, bool $pretty): string
    {
        if ($pretty) {
            return sprintf(
                '%s/%s/wp/%s/%s',
                rtrim($this->app->site(), '/'),
                $basePath,
                self::VERSION,
                ltrim($path, '/')
            );
        }

        return sprintf(
            '%s?rest_route=/wp/%s/%s',
            rtrim($this->app->site(), '/'),
            self::VERSION,
            ltrim($path, '/')
        );
    }

    /**
     * @param  array<string, array<int, string>>  $headers
     *
     * @throws HttpException
     */
    protected function error(string $message, int $code, array $headers): void
    {
        throw match ($code) {
            404 => new NotFoundException($message, $code),

            default => new HttpUnknownError($message, $code),
        };
    }
}
