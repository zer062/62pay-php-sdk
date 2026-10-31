<?php
declare(strict_types=1);

namespace Sixtytwopay\Responses;

final class CheckoutSessionResponse
{
    private function __construct(
        private string $id,
        private ?string $url,
        private string $stage,
        private ?string $externalReference,
        private ?array $invoice,
        private ?array $customer
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    /** Only present on the response that created the session. */
    public function url(): ?string
    {
        return $this->url;
    }

    public function stage(): string
    {
        return $this->stage;
    }

    public function externalReference(): ?string
    {
        return $this->externalReference;
    }

    /** @return array{id: string, status: string, amount: int, payment_method: string, installments: int}|null */
    public function invoice(): ?array
    {
        return $this->invoice;
    }

    /** @return array{name: string, email: ?string, phone: ?string, document_number: ?string}|null */
    public function customer(): ?array
    {
        return $this->customer;
    }

    public static function fromArray(array $response): self
    {
        $data = $response['data'] ?? $response;

        return new self(
            (string) $data['id'],
            $data['url'] ?? null,
            (string) $data['stage'],
            $data['external_reference'] ?? null,
            $data['invoice'] ?? null,
            $data['customer'] ?? null
        );
    }
}
