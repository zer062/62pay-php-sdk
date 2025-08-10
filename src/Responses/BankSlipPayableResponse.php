<?php
declare(strict_types=1);

namespace Sixtytwopay\Responses;

final class BankSlipPayableResponse
{
    /**
     * @param string $id
     * @param string|null $identificationField
     * @param string|null $bankSlipNumber
     * @param string|null $barcode
     * @param string|null $bankSlipUrl
     */
    private function __construct(
        private string  $id,
        private ?string $identificationField,
        private ?string $bankSlipNumber,
        private ?string $barcode,
        private ?string $bankSlipUrl
    )
    {
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function identificationField(): ?string
    {
        return $this->identificationField;
    }

    /**
     * @return string|null
     */
    public function bankSlipNumber(): ?string
    {
        return $this->bankSlipNumber;
    }

    /**
     * @return string|null
     */
    public function barcode(): ?string
    {
        return $this->barcode;
    }

    /**
     * @return string|null
     */
    public function bankSlipUrl(): ?string
    {
        return $this->bankSlipUrl;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            identificationField: $data['identification_field'] ?? null,
            bankSlipNumber: $data['bank_slip_number'] ?? null,
            barcode: $data['barcode'] ?? null,
            bankSlipUrl: $data['bank_slip_url'] ?? null
        );
    }
}
