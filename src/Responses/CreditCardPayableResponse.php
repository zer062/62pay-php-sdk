<?php
declare(strict_types=1);

namespace Sixtytwopay\Responses;

final class CreditCardPayableResponse
{
    /**
     * @param string|null $brand
     * @param string|null $lastFourDigits
     */
    private function __construct(
        private ?string $brand,
        private ?string $lastFourDigits
    )
    {
    }

    /**
     * @return string|null
     */
    public function brand(): ?string
    {
        return $this->brand;
    }

    /**
     * @return string|null
     */
    public function lastFourDigits(): ?string
    {
        return $this->lastFourDigits;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            brand: $data['brand'] ?? null,
            lastFourDigits: $data['last_four_digits'] ?? null
        );
    }
}
