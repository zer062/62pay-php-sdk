<?php

declare(strict_types=1);

namespace Sixtytwopay\Services;

use Sixtytwopay\Client;
use Sixtytwopay\Exceptions\ApiException;

final class CustomerService
{
    private const string CUSTOMER_ENDPOINT = 'customers';

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create a new customer.
     *
     * @param array $customerData Associative array with customer data.
     * @return array API response as associative array
     *
     * @throws ApiException
     */
    public function create(array $customerData): array
    {
        return $this->client->request('POST', self::CUSTOMER_ENDPOINT, [
            'json' => $this->buildPayload($customerData),
        ]);
    }

    /**
     * Update an existing customer by provider/customer ID.
     *
     * @param string $customerId Customer/provider ID
     * @param array $customerData Data to update
     * @return array API response
     *
     * @throws ApiException
     */
    public function update(string $customerId, array $customerData): array
    {
        return $this->client->request('PATCH', self::CUSTOMER_ENDPOINT . '/' . $customerId, [
            'json' => $this->buildPayload($customerData),
        ]);
    }

    /**
     * Delete a customer by ID.
     *
     * @param string $customerId
     *
     * @throws ApiException
     */
    public function delete(string $customerId): void
    {
        $this->client->request('DELETE', self::CUSTOMER_ENDPOINT . '/' . $customerId);
    }

    /**
     * Build the payload array for create/update requests.
     *
     * @param array $data Raw input data
     * @return array Formatted payload expected by the API
     */
    private function buildPayload(array $data): array
    {
        return [
            'type' => $data['type'] ?? null,
            'name' => $data['name'] ?? null,
            'legal_name' => $data['legal_name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'document_number' => $data['document_number'] ?? null,
            'address' => $data['address'] ?? null,
            'complement' => $data['complement'] ?? null,
            'address_number' => $data['address_number'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'province' => $data['province'] ?? null,
            'city' => $data['city'] ?? null,
        ];
    }
}
