<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use Illuminate\Http\Client\Response;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;
use stdClass;
use Storipress\WordPress\Exceptions\BadRequestException;
use Storipress\WordPress\Exceptions\HttpException;
use Storipress\WordPress\Exceptions\HttpUnknownError;
use Storipress\WordPress\Exceptions\NotFoundException;
use Storipress\WordPress\Exceptions\Rest\CannotCreateRestException;
use Storipress\WordPress\Exceptions\Rest\CannotUpdateRestException;
use Storipress\WordPress\Exceptions\Rest\DuplicateTermSlugRestException;
use Storipress\WordPress\Exceptions\Rest\TermExistsRestException;
use Storipress\WordPress\Exceptions\Rest\UnknownRestException;
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

        if (!($payload instanceof stdClass)) {
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
    protected function error(stdClass $payload, string $message, int $status, array $headers): void
    {
        if ($this->validate($payload)) {

            $error = ErrorException::from($payload);

            $error->raw_message = $message;

            throw match ($error->code) {
                'term_exists' => new TermExistsRestException($error, $status),
                'duplicate_term_slug' => new DuplicateTermSlugRestException($error, $status),
                'rest_cannot_create' => new CannotCreateRestException($error, $status),
                'rest_cannot_update' => new CannotUpdateRestException($error, $status),
                default => new UnknownRestException($error, $status),
            };
        }

        throw match ($status) {
            401 => new UnauthorizedException($message, $status),
            403 => new BadRequestException($message, $status),
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
