<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use Illuminate\Http\Client\Response;
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
        $response = $this
            ->app
            ->http
            ->withUserAgent($this->app->userAgent())
            ->withBasicAuth($this->app->username(), $this->app->password())
            ->{$method}($this->getUrl($path), $options);

        if (!($response instanceof Response)) {
            throw new UnexpectedValueException();
        }

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

        // @phpstan-ignore-next-line
        if (!($data instanceof stdClass) && !is_array($data)) {
            throw new UnexpectedValueException();
        }

        return $data;
    }

    public function getUrl(string $path): string
    {
        return sprintf('%s/wp-json/wp/%s/%s',
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
