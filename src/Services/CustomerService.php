<?php

declare(strict_types=1);

namespace Sixtytwopay\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sixtytwopay\Client;
use Sixtytwopay\Exceptions\ApiException;

final class CustomerService
{
    private const string CUSTOMER_ENDPOINT = 'customers';

    /**
     * @param Client $client
     */
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array $customerData
     * @return array
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(array $customerData): array
    {
        return $this->client->request('POST', self::CUSTOMER_ENDPOINT, [
            'json' => $this->buildPayload($customerData),
        ]);
    }

    /**
     * @param string $customerId
     * @param array $customerData
     * @return array
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(string $customerId, array $customerData): array
    {
        return $this->client->request('PATCH', sprintf('%s/%s', self::CUSTOMER_ENDPOINT, $customerId), [
            'json' => $this->buildPayload($customerData),
        ]);
    }

    /**
     * @param string $customerId
     * @return void
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(string $customerId): void
    {
        $this->client->request('DELETE', sprintf('%s/%s', self::CUSTOMER_ENDPOINT, $customerId));
    }


    /**
     * @param string $customerId
     * @return array
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $customerId): array
    {
        return $this->client->request('GET', sprintf('%s/%s', self::CUSTOMER_ENDPOINT, $customerId));
    }

    /**
     * @param array $filters
     * @return array
     * @throws ApiException
     * @throws GuzzleException
     */
    public function list(array $filters = []): array
    {
        return $this->client->request('GET', self::CUSTOMER_ENDPOINT, [
            'query' => $filters,
        ]);
    }

    /**
     * @param array $data
     * @return array
     */
    private function buildPayload(array $data): array
    {
        $payload = [
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

        return array_filter($payload, fn($value) => $value !== null);
    }
}
