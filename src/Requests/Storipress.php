<?php

declare(strict_types=1);

namespace Storipress\WordPress\Requests;

use Storipress\WordPress\Exceptions\HttpException;
use Storipress\WordPress\Exceptions\UnexpectedValueException;
use Storipress\WordPress\Objects\StoripressResponse;

/**
 * Storipress plugin's API endpoints
 */
class Storipress extends Request
{
    public const VERSION = 'v1';

    /**
     * @throws HttpException
     * @throws UnexpectedValueException
     */
    public function connect(string $client): StoripressResponse
    {
        $data = $this->request('post', '/connect', [
            'storipress_client' => $client,
        ]);

        if (is_array($data)) {
            throw new UnexpectedValueException();
        }

        return StoripressResponse::from($data);
    }

    /**
     * @throws HttpException
     * @throws UnexpectedValueException
     */
    public function disconnect(string $client): StoripressResponse
    {
        $data = $this->request('post', '/disconnect', [
            'storipress_client' => $client,
        ]);

        if (is_array($data)) {
            throw new UnexpectedValueException();
        }

        return StoripressResponse::from($data);
    }

    public function getUrl(string $path): string
    {
        return sprintf('%s/wp-json/storipress/%s/%s',
            rtrim($this->app->site(), '/'),
            self::VERSION,
            ltrim($path, '/')
        );
    }
}
