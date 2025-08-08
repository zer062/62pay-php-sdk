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
     * @param array $data
     * @return array
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(array $data): array
    {
        return $this->client->request('POST', self::INVOICES_ENDPOINT, [
            'json' => $this->buildCreatePayload($data),
        ]);
    }

    /**
     * @param string $invoice
     * @param array $data
     * @return array
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(string $invoice, array $data): array
    {
        return $this->client->request('PUT', sprintf('%s/%s', self::INVOICES_ENDPOINT, $invoice), [
            'json' => $this->buildUpdatePayload($data),
        ]);
    }

    /**
     * @param string $invoice
     * @return void
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(string $invoice): void
    {
        $this->client->request('DELETE', sprintf('%s/%s', self::INVOICES_ENDPOINT, $invoice));
    }

    /**
     * @param string $invoice
     * @return array
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $invoice): array
    {
        return $this->client->request('GET', sprintf('%s/%s', self::INVOICES_ENDPOINT, $invoice));
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
     * @param string $invoice
     * @return void
     * @throws ApiException
     * @throws GuzzleException
     */
    public function refund(string $invoice): void
    {
        $this->client->request('POST', sprintf('%s/%s/refund', self::INVOICES_ENDPOINT, $invoice));
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
