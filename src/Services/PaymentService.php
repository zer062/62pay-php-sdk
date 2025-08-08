<?php

declare(strict_types=1);

namespace Sixtytwopay\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sixtytwopay\Client;
use Sixtytwopay\Exceptions\ApiException;

final class PaymentService
{
    private const PAYMENTS_ENDPOINT = 'payments';

    /**
     * @param Client $client
     */
    public function __construct(private Client $client)
    {
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
        return $this->client->request('PATCH', sprintf('%s/%s', self::PAYMENTS_ENDPOINT, $invoice), [
            'json' => $this->buildUpdatePayload($data),
        ]);
    }

    /**
     * @param string $invoice
     * @return array
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $invoice): array
    {
        return $this->client->request('GET', sprintf('%s/%s', self::PAYMENTS_ENDPOINT, $invoice));
    }

    /**
     * @param string $invoice
     * @param array $data
     * @return void
     * @throws ApiException
     * @throws GuzzleException
     */
    public function refund(string $invoice, array $data): void
    {
        $this->client->request('POST', sprintf('%s/%s/refund', self::PAYMENTS_ENDPOINT, $invoice), [
            'json' => $this->buildRefundPayload($data),
        ]);
    }

    /**
     * @param array $data
     * @return array
     */
    private function buildUpdatePayload(array $data): array
    {
        $payload = [
            'interest_percenter' => $data['interest_percenter'] ?? null,
            'fine_type' => $data['fine_type'] ?? null,
            'fine_value' => $data['fine_value'] ?? null,
            'discount_type' => $data['discount_type'] ?? null,
            'discount_value' => $data['discount_value'] ?? null,
            'discount_deadline' => $data['discount_deadline'] ?? null,
            'description' => $data['description'] ?? null,
        ];

        return array_filter($payload, fn($value) => $value !== null);
    }

    /**
     * @param array $data
     * @return array
     */
    private function buildRefundPayload(array $data): array
    {
        $payload = [
            'reason' => $data['reason'] ?? null,
        ];

        return array_filter($payload, fn($value) => $value !== null);
    }
}
