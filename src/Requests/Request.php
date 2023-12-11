<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use Illuminate\Http\Client\Response;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;
use stdClass;
use Storipress\WordPress\Exceptions\BadRequestException;
use Storipress\WordPress\Exceptions\ForbiddenException;
use Storipress\WordPress\Exceptions\HttpException;
use Storipress\WordPress\Exceptions\HttpUnknownError;
use Storipress\WordPress\Exceptions\NotFoundException;
use Storipress\WordPress\Exceptions\Rest\CannotCreateException;
use Storipress\WordPress\Exceptions\Rest\CannotUpdateException;
use Storipress\WordPress\Exceptions\Rest\DuplicateTermSlugException;
use Storipress\WordPress\Exceptions\Rest\TermExistsException;
use Storipress\WordPress\Exceptions\Rest\UnknownException;
use Storipress\WordPress\Exceptions\UnauthorizedException;
use Storipress\WordPress\Exceptions\UnexpectedValueException;
use Storipress\WordPress\Objects\ErrorException;
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

        $response = $http->{$method}($this->getUrl($path), $options);

        if (!($response instanceof Response)) {
            throw new UnexpectedValueException();
        }

        $payload = $response->object();

        // @phpstan-ignore-next-line
        if (!($payload instanceof stdClass) && !is_array($payload)) {
            throw new UnexpectedValueException();
        }

        if (!$response->successful()) {
            $this->error(
                $payload,
                $response->body(),
                $response->status(),
                $response->headers(),
            );
        }

        if ($method === 'delete') {
            return $response->successful();
        }

        return $payload;
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
    protected function error(stdClass $payload, string $message, int $status, array $headers): void
    {
        if ($this->validate($payload)) {
            $error = ErrorException::from($payload);

            throw match ($error->code) {
                'term_exists' => new TermExistsException($error, $status),
                'duplicate_term_slug' => new DuplicateTermSlugException($error, $status),
                'rest_cannot_create' => new CannotCreateException($error, $status),
                'rest_cannot_update' => new CannotUpdateException($error, $status),
                default => new UnknownException($error, $status),
            };
        }

        throw match ($status) {
            400 => new BadRequestException($message, $status),
            401 => new UnauthorizedException($message, $status),
            403 => new ForbiddenException($message, $status),
            404 => new NotFoundException($message, $status),
            default => new HttpUnknownError($message, $status),
        };
    }

    protected function validate(stdClass $data): bool
    {
        $file = realpath(
            sprintf('%s/../Schemas/exception.json', __DIR__),
        );

        if ($file === false) {
            return false;
        }

        $path = sprintf('file://%s', $file);

        $validator = new Validator();

        $validator->validate($data, ['$ref' => $path], Constraint::CHECK_MODE_NORMAL | Constraint::CHECK_MODE_VALIDATE_SCHEMA);

        return $validator->isValid();
    }
}
