<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use Illuminate\Http\Client\Response;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;
use stdClass;
use Storipress\WordPress\Exceptions\BadRequestException;
use Storipress\WordPress\Exceptions\CannotCreateException;
use Storipress\WordPress\Exceptions\CannotUpdateException;
use Storipress\WordPress\Exceptions\DuplicateTermSlugException;
use Storipress\WordPress\Exceptions\ForbiddenException;
use Storipress\WordPress\Exceptions\InvalidPostIdException;
use Storipress\WordPress\Exceptions\NoRouteException;
use Storipress\WordPress\Exceptions\NotFoundException;
use Storipress\WordPress\Exceptions\TermExistsException;
use Storipress\WordPress\Exceptions\UnauthorizedException;
use Storipress\WordPress\Exceptions\UnexpectedValueException;
use Storipress\WordPress\Exceptions\UnknownException;
use Storipress\WordPress\Exceptions\WordPressException;
use Storipress\WordPress\Objects\WordPressError;
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
     * @param  array<mixed>  $headers
     * @param  array{
     *     resource: resource,
     *     mime: string
     * }|array{} $body
     * @return ($method is 'delete' ? bool : stdClass|array<int, stdClass>)
     *
     * @throws UnexpectedValueException|WordPressException
     */
    protected function request(
        string $method,
        string $path,
        array $options = [],
        array $headers = [],
        array $body = [],
    ): stdClass|array|bool {
        $http = $this->app->http
            ->withoutVerifying()
            ->withoutRedirecting()
            ->withBasicAuth($this->app->username(), $this->app->password());

        if ($this->app->userAgent()) {
            $http->withUserAgent($this->app->userAgent());
        }

        if (!empty($body)) {
            // @phpstan-ignore-next-line
            $http->withBody($body['resource'], $body['mime']);
        }

        if (!empty($headers)) {
            $http->withHeaders($headers);
        }

        $response = $http->{$method}(
            $this->getUrl(
                $path,
                $this->app->prefix(),
                $this->app->isPrettyUrl(),
            ),
            $options,
        );

        if (!($response instanceof Response)) {
            throw $this->unexpectedValueException();
        }

        $payload = $response->object();

        // @phpstan-ignore-next-line
        if (!($payload instanceof stdClass) && !is_array($payload)) {
            throw $this->unexpectedValueException();
        }

        if (!$response->successful()) {
            $this->error(
                $payload,
                $response->body(),
                $response->status(),
            );
        }

        if ($method === 'delete') {
            return $response->successful();
        }

        return $payload;
    }

    public function getUrl(string $path, string $prefix, bool $pretty): string
    {
        if ($pretty) {
            return sprintf(
                '%s/%s/wp/%s/%s',
                rtrim($this->app->site(), '/'),
                $prefix,
                self::VERSION,
                ltrim($path, '/'),
            );
        }

        return sprintf(
            '%s?rest_route=/wp/%s/%s',
            rtrim($this->app->site(), '/'),
            self::VERSION,
            ltrim($path, '/'),
        );
    }

    /**
     * @throws WordPressException
     */
    protected function error(stdClass $payload, string $message, int $status): void
    {
        if ($this->validate($payload)) {
            $error = WordPressError::from($payload);

            throw match ($error->code) {
                'term_exists' => new TermExistsException($error, $status),
                'duplicate_term_slug' => new DuplicateTermSlugException($error, $status),
                'rest_cannot_create' => new CannotCreateException($error, $status),
                'rest_cannot_update' => new CannotUpdateException($error, $status),
                'rest_no_route' => new NoRouteException($error, $status),
                'rest_post_invalid_id' => new InvalidPostIdException($error, $status),
                default => new UnknownException($error, $status),
            };
        }

        $error = WordPressError::from((object) [
            'code' => (string) $status,
            'message' => $message,
            'data' => (object) [],
        ]);

        throw match ($status) {
            400 => new BadRequestException($error),
            401 => new UnauthorizedException($error),
            403 => new ForbiddenException($error),
            404 => new NotFoundException($error),
            default => new UnknownException($error, $status),
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

    protected function unexpectedValueException(): UnexpectedValueException
    {
        return new UnexpectedValueException(WordPressError::from((object) [
            'message' => 'Unexpected value.',
            'code' => '500',
            'data' => (object) [],
        ]));
    }
}
