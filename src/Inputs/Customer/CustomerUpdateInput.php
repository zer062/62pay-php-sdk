<?php
declare(strict_types=1);

namespace Sixtytwopay\Inputs\Customer;

final class CustomerUpdateInput
{
    /**
     * @param array $fields
     */
    private function __construct(private array $fields)
    {
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $allowed = [
            'type', 'name', 'legal_name', 'email', 'phone', 'document_number',
            'address', 'complement', 'address_number', 'postal_code',
            'province', 'state', 'city', 'tags',
        ];

        $payload = array_intersect_key($data, array_flip($allowed));

        return new self($payload);
    }

    /**
     * @return array
     */
    public function toPayload(): array
    {
        return array_filter($this->fields, static fn($v) => $v !== null);
    }
}
