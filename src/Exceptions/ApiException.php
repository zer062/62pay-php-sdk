<?php

declare(strict_types=1);

namespace Sixtytwopay\Exceptions;

use Exception;
use GuzzleHttp\Exception\ConnectException;
use Throwable;

class ApiException extends Exception
{
    protected const CODE_BAD_REQUEST = 4001;
    protected const CODE_UNAUTHORIZED = 4002;
    protected const CODE_FORBIDDEN = 4003;
    protected const CODE_NOT_FOUND = 4004;
    protected const CODE_CONFLICT = 4005;
    protected const CODE_UNPROCESSABLE_ENTITY = 4006;
    protected const CODE_SERVER_ERROR = 5001;
    protected const CODE_CONNECTION = 6001;
    protected const CODE_UNEXPECTED = 7001;

    public function __construct(
        string        $message = '',
        int           $code = 0,
        ?Throwable    $previous = null,
        private ?int  $statusCode = null,
        private array $rawPayload = [],
    )
    {
        parent::__construct($message, $code, $previous);
    }

    public static function badRequest(?array $details = null, ?Throwable $previous = null): self
    {
        return new self(
            message: self::extractMessage($details) ?? 'Bad request.',
            code: self::CODE_BAD_REQUEST,
            previous: $previous,
            statusCode: 400,
            rawPayload: $details ?? [],
        );
    }

    public static function unauthorized(?array $details = null, ?Throwable $previous = null): self
    {
        return new self(
            message: self::extractMessage($details) ?? 'Unauthorized.',
            code: self::CODE_UNAUTHORIZED,
            previous: $previous,
            statusCode: 401,
            rawPayload: $details ?? [],
        );
    }

    public static function forbidden(?array $details = null, ?Throwable $previous = null): self
    {
        return new self(
            message: self::extractMessage($details) ?? 'Forbidden.',
            code: self::CODE_FORBIDDEN,
            previous: $previous,
            statusCode: 403,
            rawPayload: $details ?? [],
        );
    }

    public static function notFound(?array $details = null, ?Throwable $previous = null): self
    {
        return new self(
            message: self::extractMessage($details) ?? 'Resource not found.',
            code: self::CODE_NOT_FOUND,
            previous: $previous,
            statusCode: 404,
            rawPayload: $details ?? [],
        );
    }

    public static function conflict(?array $details = null, ?Throwable $previous = null): self
    {
        return new self(
            message: self::extractMessage($details) ?? 'Data conflict.',
            code: self::CODE_CONFLICT,
            previous: $previous,
            statusCode: 409,
            rawPayload: $details ?? [],
        );
    }

    public static function unprocessableEntity(?array $details = null, ?Throwable $previous = null): self
    {
        return new self(
            message: self::extractMessage($details) ?? 'Unprocessable entity.',
            code: self::CODE_UNPROCESSABLE_ENTITY,
            previous: $previous,
            statusCode: 422,
            rawPayload: $details ?? [],
        );
    }

    public static function serverError(
        int        $statusCode,
        ?array     $body = null,
        ?Throwable $previous = null
    ): self
    {
        return new self(
            message: self::extractMessage($body) ?? 'Server error.',
            code: self::CODE_SERVER_ERROR,
            previous: $previous,
            statusCode: $statusCode,
            rawPayload: $body ?? [],
        );
    }

    public static function connection(?ConnectException $previous = null): self
    {
        return new self(
            message: 'Connection error: unable to reach external service.',
            code: self::CODE_CONNECTION,
            previous: $previous,
            statusCode: null,
            rawPayload: [],
        );
    }

    public static function unexpected(?Throwable $previous = null): self
    {
        return new self(
            message: $previous?->getMessage() ?: 'Unexpected error communicating with external service.',
            code: self::CODE_UNEXPECTED,
            previous: $previous,
            statusCode: null,
            rawPayload: [],
        );
    }

    public static function fromHttpResponse(
        int        $statusCode,
        ?array     $body = null,
        ?Throwable $previous = null
    ): self
    {
        return match ($statusCode) {
            400 => self::badRequest($body, $previous),
            401 => self::unauthorized($body, $previous),
            403 => self::forbidden($body, $previous),
            404 => self::notFound($body, $previous),
            409 => self::conflict($body, $previous),
            422 => self::unprocessableEntity($body, $previous),
            default => self::serverError($statusCode, $body, $previous),
        };
    }

    public function statusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getRawPayload(): array
    {
        return $this->rawPayload;
    }

    public function payload(): array
    {
        return $this->rawPayload;
    }

    public function response(): array
    {
        return $this->rawPayload;
    }

    public function errors(): array
    {
        return $this->rawPayload['errors'] ?? [];
    }

    public function getFieldErrors(): array
    {
        return $this->rawPayload['errors'][0]['meta']['fields'] ?? [];
    }

    public function getApiErrorCode(): ?string
    {
        $code = $this->rawPayload['code']
            ?? $this->rawPayload['error']['code']
            ?? $this->rawPayload['errors'][0]['code']
            ?? $this->rawPayload[0]['code']
            ?? null;

        return $code !== null ? (string)$code : null;
    }

    private static function extractMessage(?array $payload): ?string
    {
        if (!$payload) {
            return null;
        }

        $message = $payload['message']
            ?? $payload['error']['message']
            ?? $payload['errors'][0]['description']
            ?? $payload[0]['description']
            ?? null;

        if ($message !== null) {
            return (string)$message;
        }

        $fields = $payload['errors'][0]['meta']['fields'] ?? null;

        if (!is_array($fields)) {
            return null;
        }

        foreach ($fields as $messages) {
            if (is_array($messages) && isset($messages[0])) {
                return (string)$messages[0];
            }

            if (is_string($messages)) {
                return $messages;
            }
        }

        return null;
    }
}
