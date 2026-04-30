<?php

declare(strict_types=1);

namespace Sixtytwopay\Responses;

use Sixtytwopay\Exceptions\ApiException;

final class InvoiceCreditCardPaymentResponse
{
    private function __construct(
        private bool                       $success,
        private ?InvoiceResponse           $invoice = null,
        private ?CreditCardPaymentResponse $payment = null,
        private ?string                    $message = null,
        private ?string                    $code = null,
        private ?int                       $statusCode = null,
        private array                      $raw = [],
    )
    {
    }

    public function success(): bool
    {
        return $this->success;
    }

    public function failed(): bool
    {
        return !$this->success;
    }

    public function invoice(): ?InvoiceResponse
    {
        return $this->invoice;
    }

    public function payment(): ?CreditCardPaymentResponse
    {
        return $this->payment;
    }

    public function message(): ?string
    {
        return $this->message;
    }

    public function code(): ?string
    {
        return $this->code;
    }

    public function statusCode(): ?int
    {
        return $this->statusCode;
    }

    public function raw(): array
    {
        return $this->raw;
    }

    public function fieldErrors(): array
    {
        return $this->raw['errors'][0]['meta']['fields'] ?? [];
    }

    public function fieldError(string $field): array
    {
        $errors = $this->fieldErrors();

        return isset($errors[$field]) && is_array($errors[$field])
            ? $errors[$field]
            : [];
    }

    public static function fromArray(array $response): self
    {
        $success = !isset($response['errors'])
            && (bool)($response['success'] ?? true);

        if (!$success) {
            return new self(
                success: false,
                message: self::extractMessage($response),
                code: self::extractCode($response),
                statusCode: isset($response['status_code']) ? (int)$response['status_code'] : null,
                raw: $response,
            );
        }

        $data = $response['data'] ?? $response;

        $invoice = null;

        if (isset($data['invoice']) && is_array($data['invoice'])) {
            $invoice = InvoiceResponse::fromArray($data['invoice']);
        }

        $payment = null;

        if (isset($data['payment']) && is_array($data['payment'])) {
            $payment = CreditCardPaymentResponse::fromArray($data['payment']);
        }

        return new self(
            success: true,
            invoice: $invoice,
            payment: $payment,
            raw: $response,
        );
    }

    public static function fromApiException(ApiException $exception): self
    {
        $payload = self::extractPayloadFromException($exception);

        if ($payload !== []) {
            return new self(
                success: false,
                message: self::extractMessage($payload) ?? $exception->getMessage(),
                code: self::extractCode($payload) ?? self::extractCodeFromException($exception),
                statusCode: self::extractStatusCodeFromException($exception),
                raw: $payload,
            );
        }

        $fieldErrors = method_exists($exception, 'getFieldErrors')
            ? $exception->getFieldErrors()
            : [];

        if (!empty($fieldErrors)) {
            $payload = [
                'errors' => [
                    [
                        'code' => 'validation_error',
                        'description' => $exception->getMessage(),
                        'meta' => [
                            'fields' => $fieldErrors,
                        ],
                    ],
                ],
            ];

            return new self(
                success: false,
                message: $exception->getMessage(),
                code: 'validation_error',
                statusCode: self::extractStatusCodeFromException($exception),
                raw: $payload,
            );
        }

        return new self(
            success: false,
            message: $exception->getMessage(),
            code: self::extractCodeFromException($exception),
            statusCode: self::extractStatusCodeFromException($exception),
            raw: [],
        );
    }

    private static function extractPayloadFromException(ApiException $exception): array
    {
        if (method_exists($exception, 'getRawPayload')) {
            $payload = $exception->getRawPayload();

            if (is_array($payload) && !empty($payload)) {
                return $payload;
            }
        }

        if (method_exists($exception, 'response')) {
            $response = $exception->response();

            if (is_array($response) && !empty($response)) {
                return $response;
            }
        }

        if (method_exists($exception, 'payload')) {
            $payload = $exception->payload();

            if (is_array($payload) && !empty($payload)) {
                return $payload;
            }
        }

        if (method_exists($exception, 'errors')) {
            $errors = $exception->errors();

            if (is_array($errors) && !empty($errors)) {
                if (isset($errors['errors'])) {
                    return $errors;
                }

                return [
                    'errors' => $errors,
                ];
            }
        }

        $previous = $exception->getPrevious();

        if ($previous instanceof ApiException) {
            $payload = self::extractPayloadFromException($previous);

            if ($payload !== []) {
                return $payload;
            }
        }

        $message = trim($exception->getMessage());

        if ($message === '') {
            return [];
        }

        $decoded = json_decode($message, true);

        return is_array($decoded) ? $decoded : [];
    }

    private static function extractMessage(array $payload): ?string
    {
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

    private static function extractCode(array $payload): ?string
    {
        $code = $payload['code']
            ?? $payload['error']['code']
            ?? $payload['errors'][0]['code']
            ?? $payload[0]['code']
            ?? null;

        return $code !== null ? (string)$code : null;
    }

    private static function extractCodeFromException(ApiException $exception): ?string
    {
        if (method_exists($exception, 'getApiErrorCode')) {
            $code = $exception->getApiErrorCode();

            if ($code !== null) {
                return $code;
            }
        }

        $previous = $exception->getPrevious();

        if ($previous instanceof ApiException) {
            return self::extractCodeFromException($previous);
        }

        return null;
    }

    private static function extractStatusCodeFromException(ApiException $exception): ?int
    {
        if (method_exists($exception, 'statusCode')) {
            $statusCode = $exception->statusCode();

            if (is_numeric($statusCode)) {
                return (int)$statusCode;
            }
        }

        $previous = $exception->getPrevious();

        if ($previous instanceof ApiException) {
            return self::extractStatusCodeFromException($previous);
        }

        return null;
    }
}
