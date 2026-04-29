<?php

declare(strict_types=1);

namespace Sixtytwopay\Inputs\Invoice;

final class InvoiceCreditCardInput
{
    public function __construct(
        private string $holderName,
        private string $number,
        private string $cardExpiryDate,
        private string $ccv,
        private string $billingName,
        private string $billingEmail,
        private string $billingDocumentNumber,
        private string $billingPostalCode,
        private string $billingStreet,
        private string $billingAddressNumber,
        private string $billingAddressComplement,
        private string $billingNeighborhood,
        private string $billingCity,
        private string $billingState,
        private string $billingPhone,
        private ?int   $installments = null,
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            holderName: (string)$data['holder_name'],
            number: (string)$data['number'],
            cardExpiryDate: (string)$data['card_expiry_date'],
            ccv: (string)$data['ccv'],
            billingName: (string)$data['billing_name'],
            billingEmail: (string)$data['billing_email'],
            billingDocumentNumber: (string)$data['billing_document_number'],
            billingPostalCode: (string)$data['billing_postal_code'],
            billingStreet: (string)$data['billing_street'],
            billingAddressNumber: (string)$data['billing_address_number'],
            billingAddressComplement: (string)$data['billing_address_complement'],
            billingNeighborhood: (string)$data['billing_neighborhood'],
            billingCity: (string)$data['billing_city'],
            billingState: (string)$data['billing_state'],
            billingPhone: (string)$data['billing_phone'],
            installments: isset($data['installments']) ? (int)$data['installments'] : null,
        );
    }

    public function toPayload(): array
    {
        $payload = [
            'holder_name' => $this->holderName,
            'number' => $this->number,
            'card_expiry_date' => $this->cardExpiryDate,
            'ccv' => $this->ccv,
            'installments' => $this->installments,
            'billing_name' => $this->billingName,
            'billing_email' => $this->billingEmail,
            'billing_document_number' => $this->billingDocumentNumber,
            'billing_postal_code' => $this->billingPostalCode,
            'billing_street' => $this->billingStreet,
            'billing_address_number' => $this->billingAddressNumber,
            'billing_address_complement' => $this->billingAddressComplement,
            'billing_neighborhood' => $this->billingNeighborhood,
            'billing_city' => $this->billingCity,
            'billing_state' => strtoupper($this->billingState),
            'billing_phone' => $this->billingPhone,
        ];

        return array_filter($payload, static fn($value): bool => $value !== null);
    }
}
