<?php
declare(strict_types=1);

namespace Sixtytwopay\Inputs\Customer;

final class CustomerCreateInput
{
    /**
     * @param string|null $type
     * @param string|null $name
     * @param string|null $legalName
     * @param string|null $email
     * @param string|null $phone
     * @param string|null $documentNumber
     * @param string|null $address
     * @param string|null $complement
     * @param string|null $addressNumber
     * @param string|null $postalCode
     * @param string|null $province
     * @param string|null $state
     * @param string|null $city
     * @param array|null $tags
     */
    private function __construct(
        private ?string $type,
        private ?string $name,
        private ?string $legalName,
        private ?string $email,
        private ?string $phone,
        private ?string $documentNumber,
        private ?string $address,
        private ?string $complement,
        private ?string $addressNumber,
        private ?string $postalCode,
        private ?string $province,
        private ?string $state,
        private ?string $city,
        private ?array  $tags
    )
    {
    }

    /**
     * @param array $data
     * @return self
     */
    public static function make(array $data): self
    {
        return new self(
            $data['type'] ?? null,
            $data['name'] ?? null,
            $data['legal_name'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['document_number'] ?? null,
            $data['address'] ?? null,
            $data['complement'] ?? null,
            $data['address_number'] ?? null,
            $data['postal_code'] ?? null,
            $data['province'] ?? null,
            $data['state'] ?? null,
            $data['city'] ?? null,
            isset($data['tags']) && is_array($data['tags']) ? array_values($data['tags']) : null
        );
    }

    /**
     * @return array
     */
    public function toPayload(): array
    {
        $payload = [
            'type' => $this->type,
            'name' => $this->name,
            'legal_name' => $this->legalName,
            'email' => $this->email,
            'phone' => $this->phone,
            'document_number' => $this->documentNumber,
            'address' => $this->address,
            'complement' => $this->complement,
            'address_number' => $this->addressNumber,
            'postal_code' => $this->postalCode,
            'province' => $this->province,
            'state' => $this->state,
            'city' => $this->city,
            'tags' => $this->tags,
        ];

        return array_filter($payload, static fn($v) => $v !== null);
    }
}
