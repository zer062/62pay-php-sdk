<?php
declare(strict_types=1);

namespace Sixtytwopay\Responses;

final class CustomerResponse
{
    /**
     * @param string $id
     * @param string $type
     * @param string $name
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
        public string  $id,
        public string  $type,
        public string  $name,
        public ?string $legalName,
        public ?string $email,
        public ?string $phone,
        public ?string $documentNumber,
        public ?string $address,
        public ?string $complement,
        public ?string $addressNumber,
        public ?string $postalCode,
        public ?string $province,
        public ?string $state,
        public ?string $city,
        public ?array  $tags,
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
            id: $data['id'],
            type: $data['type'],
            name: $data['name'],
            legalName: $data['legal_name'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            documentNumber: $data['document_number'] ?? null,
            address: $data['address'] ?? null,
            complement: $data['complement'] ?? null,
            addressNumber: $data['address_number'] ?? null,
            postalCode: $data['postal_code'] ?? null,
            province: $data['province'] ?? null,
            state: $data['state'] ?? null,
            city: $data['city'] ?? null,
            tags: isset($a['tags']) && is_array($a['tags']) ? array_values($a['tags']) : null,
        );
    }
}
