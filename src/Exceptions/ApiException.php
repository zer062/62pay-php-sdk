<?php

declare(strict_types=1);

namespace Sixtytwopay\Exceptions;

use Exception;
use GuzzleHttp\Exception\ConnectException;

class ApiException extends Exception
{
    protected const int CODE_BAD_REQUEST = 4001;
    protected const int CODE_UNAUTHORIZED = 4002;
    protected const int CODE_FORBIDDEN = 4003;
    protected const int CODE_NOT_FOUND = 4004;
    protected const int CODE_CONFLICT = 4005;
    protected const int CODE_SERVER_ERROR = 5001;
    protected const int CODE_CONNECTION = 6001;
    protected const int CODE_UNEXPECTED = 7001;

    /**
     * @param array|null $details
     * @param Exception|null $previous
     * @return self
     */
    public static function badRequest(?array $details = null, ?Exception $previous = null): self
    {
        $message = self::extractMessage($details) ?? 'Bad request.';
        return new self($message, self::CODE_BAD_REQUEST, $previous);
    }

    /**
     * @param Exception|null $previous
     * @return self
     */
    public static function unauthorized(?Exception $previous = null): self
    {
        return new self('Unauthorized.', self::CODE_UNAUTHORIZED, $previous);
    }

    /**
     * @param Exception|null $previous
     * @return self
     */
    public static function forbidden(?Exception $previous = null): self
    {
        return new self('Forbidden.', self::CODE_FORBIDDEN, $previous);
    }

    /**
     * @param Exception|null $previous
     * @return self
     */
    public static function notFound(?Exception $previous = null): self
    {
        return new self('Resource not found.', self::CODE_NOT_FOUND, $previous);
    }

    /**
     * @param Exception|null $previous
     * @return self
     */
    public static function conflict(?Exception $previous = null): self
    {
        return new self('Data conflict.', self::CODE_CONFLICT, $previous);
    }

    /**
     * @param int $statusCode
     * @param array|null $body
     * @param Exception|null $previous
     * @return self
     */
    public static function serverError(int $statusCode, ?array $body = null, ?Exception $previous = null): self
    {
        $message = self::extractMessage($body) ?? 'Server error.';
        return new self($message, self::CODE_SERVER_ERROR, $previous);
    }

    /**
     * @param ConnectException|null $previous
     * @return self
     */
    public static function connection(?ConnectException $previous = null): self
    {
        return new self('Connection error: unable to reach external service.', self::CODE_CONNECTION, $previous);
    }

    /**
     * @param Exception|null $previous
     * @return self
     */
    public static function unexpected(?Exception $previous = null): self
    {
        return new self('Unexpected error communicating with external service.', self::CODE_UNEXPECTED, $previous);
    }

    /**
     * @param int $statusCode
     * @param array|null $body
     * @param Exception|null $previous
     * @return self
     */
    public static function fromHttpResponse(int $statusCode, ?array $body = null, ?Exception $previous = null): self
    {
        return match ($statusCode) {
            400 => self::badRequest($body, $previous),
            401 => self::unauthorized($previous),
            403 => self::forbidden($previous),
            404 => self::notFound($previous),
            409 => self::conflict($previous),
            default => self::serverError($statusCode, $body, $previous),
        };
    }

    /**
     * @param array|null $payload
     * @return string|null
     */
    private static function extractMessage(?array $payload): ?string
    {
        if (!$payload) {
            return null;
        }

        if (isset($payload['message'])) {
            return $payload['message'];
        }

        if (isset($payload['error']['message'])) {
            return $payload['error']['message'];
        }

        if (isset($payload['errors'][0]['description'])) {
            return $payload['errors'][0]['description'];
        }

        if (isset($payload[0]['description'])) {
            return $payload[0]['description'];
        }

        return null;
    }
}
