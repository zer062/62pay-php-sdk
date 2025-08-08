<?php

declare(strict_types=1);

namespace Sixtytwopay\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sixtytwopay\Client;
use Sixtytwopay\Exceptions\ApiException;

final class InvoiceService
{
    private const INVOICES_ENDPOINT = 'invoices';

    /**
     * @param Client $client
     */
    public function __construct(private Client $client)
    {
    }

    /**
     * @param array $invoiceData
     * @return array
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(array $invoiceData): array
    {
        return $this->client->request('POST', self::INVOICES_ENDPOINT, [
            'json' => $this->buildCreatePayload($invoiceData),
        ]);
    }

    /**
     * @param string $invoiceId
     * @param array $invoiceData
     * @return array
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(string $invoiceId, array $invoiceData): array
    {
        return $this->client->request('PATCH', sprintf('%s/%s', self::INVOICES_ENDPOINT, $invoiceId), [
            'json' => $this->buildUpdatePayload($invoiceData),
        ]);
    }

    /**
     * @param string $invoiceId
     * @return void
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(string $invoiceId): void
    {
        $this->client->request('DELETE', sprintf('%s/%s', self::INVOICES_ENDPOINT, $invoiceId));
    }

    /**
     * @param string $invoiceId
     * @return array
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $invoiceId): array
    {
        return $this->client->request('GET', sprintf('%s/%s', self::INVOICES_ENDPOINT, $invoiceId));
    }

    /**
     * @param array $filters
     * @return array
     * @throws ApiException
     * @throws GuzzleException
     */
    public function list(array $filters = []): array
    {
        return $this->client->request('GET', self::INVOICES_ENDPOINT, [
            'query' => $filters,
        ]);
    }

    /**
     * @param string $invoiceId
     * @return void
     * @throws ApiException
     * @throws GuzzleException
     */
    public function refund(string $invoiceId): void
    {
        $this->client->request('POST', sprintf('%s/%s/refund', self::INVOICES_ENDPOINT, $invoiceId));
    }

    /**
     * @param array $data
     * @return array
     */
    private function buildCreatePayload(array $data): array
    {
        $payload = [
            'customer' => $data['customer'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
            'amount' => $data['amount'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'description' => $data['description'] ?? null,
            'installments' => $data['installments'] ?? null,
            'immutable' => $data['immutable'] ?? null,
            'interest_percent' => $data['interest_percent'] ?? null,
            'fine_type' => $data['fine_type'] ?? null,
            'fine_value' => $data['fine_value'] ?? null,
            'discount_type' => $data['discount_type'] ?? null,
            'discount_value' => $data['discount_value'] ?? null,
            'discount_deadline' => $data['discount_deadline'] ?? null,
            'tags' => isset($data['tags']) && is_array($data['tags']) ? $data['tags'] : null,
        ];

        return array_filter($payload, fn($value) => $value !== null);
    }

    /**
     * @param array $data
     * @return array
     */
    private function buildUpdatePayload(array $data): array
    {
        $payload = [
            'payment_method' => $data['payment_method'] ?? null,
            'amount' => $data['amount'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'description' => $data['description'] ?? null,
            'installments' => $data['installments'] ?? null,
            'immutable' => $data['immutable'] ?? null,
            'tags' => isset($data['tags']) && is_array($data['tags']) ? $data['tags'] : null,
        ];

        return array_filter($payload, fn($value) => $value !== null);
    }
}
