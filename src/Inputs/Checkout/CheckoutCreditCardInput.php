<?php
declare(strict_types=1);

namespace Sixtytwopay\Inputs\Checkout;

final class CheckoutCreditCardInput
{
    /**
     * @param string $holderName
     * @param string $number
     * @param string $cardExpiryDate
     * @param string $ccv
     * @param int|null $installments
     * @param string|null $billingName
     * @param string|null $billingEmail
     * @param string|null $billingDocumentNumber
     * @param string|null $billingPostalCode
     * @param string|null $billingAddressNumber
     * @param string|null $billingAddressComplement
     * @param string|null $billingPhone
     */
    public function __construct(
        private string  $holderName,
        private string  $number,
        private string  $cardExpiryDate,
        private string  $ccv,
        private ?int    $installments = null,
        private ?string $billingName = null,
        private ?string $billingEmail = null,
        private ?string $billingDocumentNumber = null,
        private ?string $billingPostalCode = null,
        private ?string $billingAddressNumber = null,
        private ?string $billingAddressComplement = null,
        private ?string $billingPhone = null,
    )
    {
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            holderName: $data['holder_name'],
            number: $data['number'],
            cardExpiryDate: $data['card_expiry_date'],
            ccv: $data['ccv'],
            installments: $data['installments'] ?? null,
            billingName: $data['billing_name'] ?? null,
            billingEmail: $data['billing_email'] ?? null,
            billingDocumentNumber: $data['billing_document_number'] ?? null,
            billingPostalCode: $data['billing_postal_code'] ?? null,
            billingAddressNumber: $data['billing_address_number'] ?? null,
            billingAddressComplement: $data['billing_address_complement'] ?? null,
            billingPhone: $data['billing_phone'] ?? null,
        );
    }

    /**
     * @return array
     */
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
            'billing_address_number' => $this->billingAddressNumber,
            'billing_address_complement' => $this->billingAddressComplement,
            'billing_phone' => $this->billingPhone,
        ];

        return array_filter($payload, static fn($v) => $v !== null);
    }
}
