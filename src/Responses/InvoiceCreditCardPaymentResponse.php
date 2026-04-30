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

    public static function fromArray(array $response): self
    {
        $success = (bool)($response['success'] ?? true);

        if (!$success) {
            return new self(
                success: false,
                message: self::extractMessage($response),
                code: isset($response['code']) && $response['code'] !== null ? (string)$response['code'] : null,
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
                code: self::extractCode($payload),
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
            code: null,
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

        $message = trim($exception->getMessage());

        if ($message === '') {
            return [];
        }

        $decoded = json_decode($message, true);

        return is_array($decoded) ? $decoded : [];
    }

    private static function extractMessage(array $payload): ?string
    {
        return $payload['message']
            ?? $payload['error']['message']
            ?? $payload['errors'][0]['description']
            ?? $payload[0]['description']
            ?? null;
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

    private static function extractStatusCodeFromException(ApiException $exception): ?int
    {
        if (method_exists($exception, 'statusCode')) {
            $statusCode = $exception->statusCode();

            return is_numeric($statusCode) ? (int)$statusCode : null;
        }

        $code = $exception->getCode();

        return $code > 0 ? (int)$code : null;
    }
}
