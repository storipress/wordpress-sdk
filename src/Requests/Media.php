<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use Illuminate\Http\UploadedFile;
use Storipress\WordPress\Exceptions\WordPressException;
use Storipress\WordPress\Objects\Media as MediaObject;

class Media extends Request
{
    /**
     * https://developer.wordpress.org/rest-api/reference/media/#list-media
     *
     * @param  array<string, mixed>  $arguments
     * @return array<int, MediaObject>
     *
     * @throws WordPressException
     */
    public function list(array $arguments = []): array
    {
        $data = $this->request('get', '/media', $arguments);

        if (! is_array($data)) {
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
    public function create(UploadedFile $file, array $arguments): MediaObject
    {
        $arguments['file'] = $file;

        $data = $this->request('post', '/media', $arguments);

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
