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
        private string  $id,
        private string  $type,
        private string  $name,
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
        private ?array  $tags,
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
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function legalName(): ?string
    {
        return $this->legalName;
    }

    /**
     * @return string|null
     */
    public function email(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function phone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return string|null
     */
    public function documentNumber(): ?string
    {
        return $this->documentNumber;
    }

    /**
     * @return string|null
     */
    public function address(): ?string
    {
        return $this->address;
    }

    /**
     * @return string|null
     */
    public function complement(): ?string
    {
        return $this->complement;
    }

    /**
     * @return string|null
     */
    public function addressNumber(): ?string
    {
        return $this->addressNumber;
    }

    /**
     * @return string|null
     */
    public function postalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @return string|null
     */
    public function province(): ?string
    {
        return $this->province;
    }

    /**
     * @return string|null
     */
    public function state(): ?string
    {
        return $this->state;
    }

    /**
     * @return string|null
     */
    public function city(): ?string
    {
        return $this->city;
    }

    /**
     * @return array|null
     */
    public function tags(): ?array
    {
        return $this->tags;
    }

    /**
     * @param array $response
     * @return self
     */
    public static function fromArray(array $response): self
    {
        $data = $response['data'] ?? $response;

        $address = $data['address'] ?? [];
        if (!is_array($address)) {
            $address = [];
        }

        return new self(
            id: $data['id'],
            type: $data['type'],
            name: $data['name'],
            legalName: $data['legal_name'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            documentNumber: $data['document_number'] ?? null,
            address: $address['address'] ?? null,
            complement: $address['complement'] ?? null,
            addressNumber: $address['address_number'] ?? null,
            postalCode: $address['postal_code'] ?? null,
            province: $address['province'] ?? null,
            state: $address['state'] ?? null,
            city: $address['city'] ?? null,
            tags: isset($data['tags']) && is_array($data['tags']) ? array_values($data['tags']) : null
        );
    }
}
