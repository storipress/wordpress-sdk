<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use Storipress\WordPress\Exceptions\CannotOpenResourceException;
use Storipress\WordPress\Exceptions\WordPressException;
use Storipress\WordPress\Objects\Media as MediaObject;
use Storipress\WordPress\Objects\WordPressError;

class Media extends Request
{
    /**
     * https://developer.wordpress.org/rest-api/reference/media/#list-media
     *
     * @return array<int, MediaObject>
     *
     * @throws WordPressException
     */
    public function list(): array
    {
        $data = $this->request('get', '/media');

        if (!is_array($data)) {
            throw $this->unexpectedValueException();
        }

        return array_map(
            fn ($data) => MediaObject::from($data),
            $data,
        );
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/media/#create-a-media-item
     *
     * @param  array<string, mixed>  $arguments
     *
     * @throws WordPressException
     */
    public function create(string $path, array $arguments): MediaObject
    {
        $mime = mime_content_type($path);

        $filename = basename($path);

        $file = fopen($path, 'r');

        if ($file === false || $mime === false) {
            $error = WordPressError::from((object) [
                'code' => '400',
                'message' => 'Can\'t open resource.',
                'data' => (object) [],
            ]);

            throw new CannotOpenResourceException($error, 400);
        }

        $contentDisposition = sprintf('attachment; filename="%s"', $filename);

        $uri = sprintf('/media?%s', http_build_query($arguments));

        $data = $this->request(
            method: 'post',
            path: $uri,
            headers: [
                'Content-Disposition' => $contentDisposition,
            ],
            body: [
                'resource' => $file,
                'mime' => $mime,
            ]
        );

        if (is_array($data)) {
            throw $this->unexpectedValueException();
        }

        return MediaObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/media/#retrieve-a-media-item
     *
     *
     * @throws WordPressException
     */
    public function retrieve(int $mediaId, string $context = 'view'): MediaObject
    {
        $uri = sprintf('/media/%d', $mediaId);

        $data = $this->request('get', $uri, [
            'context' => $context,
        ]);

        if (is_array($data)) {
            throw $this->unexpectedValueException();
        }

        return MediaObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/media/#update-a-media-item
     *
     * @param  array<string, mixed>  $arguments
     *
     * @throws WordPressException
     */
    public function update(int $mediaId, array $arguments): MediaObject
    {
        $uri = sprintf('/media/%d', $mediaId);

        $data = $this->request('patch', $uri, $arguments);

        if (is_array($data)) {
            throw $this->unexpectedValueException();
        }

        return MediaObject::from($data);
    }

    /**
     * https://developer.wordpress.org/rest-api/reference/media/#delete-a-media-item
     *
     * @throws WordPressException
     */
    public function delete(int $mediaId): bool
    {
        $uri = sprintf('/media/%s', $mediaId);

        return $this->request('delete', $uri, [
            'force' => true,
        ]);
    }
}
