<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;
use stdClass;
use Storipress\WordPress\Exceptions\BadRequestException;
use Storipress\WordPress\Exceptions\CannotCreateException;
use Storipress\WordPress\Exceptions\CannotEditException;
use Storipress\WordPress\Exceptions\CannotUpdateException;
use Storipress\WordPress\Exceptions\DuplicateTermSlugException;
use Storipress\WordPress\Exceptions\ForbiddenException;
use Storipress\WordPress\Exceptions\IncorrectPasswordException;
use Storipress\WordPress\Exceptions\InvalidAuthorIdException;
use Storipress\WordPress\Exceptions\InvalidParamException;
use Storipress\WordPress\Exceptions\InvalidPostIdException;
use Storipress\WordPress\Exceptions\InvalidPostPageNumberException;
use Storipress\WordPress\Exceptions\InvalidUserIdException;
use Storipress\WordPress\Exceptions\InvalidUserSlugException;
use Storipress\WordPress\Exceptions\NoRouteException;
use Storipress\WordPress\Exceptions\NotFoundException;
use Storipress\WordPress\Exceptions\PostAlreadyTrashedException;
use Storipress\WordPress\Exceptions\RestForbiddenException;
use Storipress\WordPress\Exceptions\TermExistsException;
use Storipress\WordPress\Exceptions\UnauthorizedException;
use Storipress\WordPress\Exceptions\UnexpectedValueException;
use Storipress\WordPress\Exceptions\UnknownException;
use Storipress\WordPress\Exceptions\UploadUnknownErrorException;
use Storipress\WordPress\Exceptions\UserEmailExistsException;
use Storipress\WordPress\Exceptions\UsernameExistsException;
use Storipress\WordPress\Exceptions\WordPressException;
use Storipress\WordPress\Exceptions\WpDieException;
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
     * @param  array<string, mixed>  $options
     * @return ($method is 'delete' ? bool : stdClass|array<int, stdClass>)
     *
     * @throws UnexpectedValueException|WordPressException
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

        if (isset($options['file']) && $options['file'] instanceof UploadedFile) {
            $http->attach('file', $options['file']->getContent(), $options['file']->getClientOriginalName());

            unset($options['file']);
        }

        $response = $http->{$method}(
            $this->getUrl(
                $path,
                $this->app->prefix(),
                $this->app->isPrettyUrl(),
            ),
            $options,
        );

        if (! ($response instanceof Response)) {
            throw $this->unexpectedValueException();
        }

        $payload = $response->object();

        // @phpstan-ignore-next-line
        if (! ($payload instanceof stdClass) && ! is_array($payload)) {
            throw $this->unexpectedValueException();
        }

        if (! $response->successful()) {
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
                rtrim($this->app->url(), '/'),
                $prefix,
                self::VERSION,
                ltrim($path, '/'),
            );
        }

        return sprintf(
            '%s?rest_route=/wp/%s/%s',
            rtrim($this->app->url(), '/'),
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
                'duplicate_term_slug' => new DuplicateTermSlugException($error, $status),
                'existing_user_email' => new UserEmailExistsException($error, $status),
                'existing_user_login' => new UsernameExistsException($error, $status),
                'incorrect_password' => new IncorrectPasswordException($error, $status),
                'rest_already_trashed' => new PostAlreadyTrashedException($error, $status),
                'rest_cannot_create' => new CannotCreateException($error, $status),
                'rest_cannot_edit' => new CannotEditException($error, $status),
                'rest_cannot_update' => new CannotUpdateException($error, $status),
                'rest_forbidden' => new RestForbiddenException($error, $status),
                'rest_invalid_author' => new InvalidAuthorIdException($error, $status),
                'rest_invalid_param' => new InvalidParamException($error, $status),
                'rest_no_route' => new NoRouteException($error, $status),
                'rest_post_invalid_id' => new InvalidPostIdException($error, $status),
                'rest_post_invalid_page_number' => new InvalidPostPageNumberException($error, $status),
                'rest_upload_unknown_error' => new UploadUnknownErrorException($error, $status),
                'rest_user_invalid_id' => new InvalidUserIdException($error, $status),
                'rest_user_invalid_slug' => new InvalidUserSlugException($error, $status),
                'term_exists' => new TermExistsException($error, $status),
                'wp_die' => new WpDieException($error, $status),
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
