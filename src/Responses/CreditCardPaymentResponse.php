<?php

declare(strict_types=1);

namespace Sixtytwopay\Responses;

final class CreditCardPaymentResponse
{
    private function __construct(
        private ?string $status,
        private bool    $finalized,
        private ?string $lastFourDigits,
        private ?string $brand,
        private bool    $requiresAction,
        private array   $raw = [],
    )
    {
    }

    public function status(): ?string
    {
        return $this->status;
    }

    public function finalized(): bool
    {
        return $this->finalized;
    }

    public function lastFourDigits(): ?string
    {
        return $this->lastFourDigits;
    }

    public function brand(): ?string
    {
        return $this->brand;
    }

    public function requiresAction(): bool
    {
        return $this->requiresAction;
    }

    public function raw(): array
    {
        return $this->raw;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            status: isset($data['status']) ? (string)$data['status'] : null,
            finalized: (bool)($data['finalized'] ?? false),
            lastFourDigits: isset($data['last_four_digits']) ? (string)$data['last_four_digits'] : null,
            brand: isset($data['brand']) ? (string)$data['brand'] : null,
            requiresAction: (bool)($data['requires_action'] ?? false),
            raw: $data,
        );
    }
}
