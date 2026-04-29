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
                message: isset($response['message']) ? (string)$response['message'] : null,
                code: isset($response['code']) ? (string)$response['code'] : null,
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
                message: isset($payload['message']) ? (string)$payload['message'] : $exception->getMessage(),
                code: isset($payload['code']) ? (string)$payload['code'] : null,
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
        if (method_exists($exception, 'response')) {
            $response = $exception->response();

            if (is_array($response)) {
                return $response;
            }
        }

        if (method_exists($exception, 'payload')) {
            $payload = $exception->payload();

            if (is_array($payload)) {
                return $payload;
            }
        }

        if (method_exists($exception, 'errors')) {
            $errors = $exception->errors();

            if (is_array($errors)) {
                return $errors;
            }
        }

        $message = trim($exception->getMessage());

        if ($message === '') {
            return [];
        }

        $decoded = json_decode($message, true);

        return is_array($decoded) ? $decoded : [];
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
