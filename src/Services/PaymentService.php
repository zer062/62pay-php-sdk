<?php

declare(strict_types=1);

namespace Sixtytwopay\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sixtytwopay\Client;
use Sixtytwopay\Exceptions\ApiException;
use Sixtytwopay\Responses\PaymentResponse;

final class PaymentService
{
    private const PAYMENTS_ENDPOINT = 'payments';

    public function __construct(private Client $client)
    {
    }

    /**
     * @param string $paymentPublicId
     * @param array $data
     * @return PaymentResponse
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(string $paymentPublicId, array $data): PaymentResponse
    {
        $response = $this->client->request('PATCH', sprintf('%s/%s', self::PAYMENTS_ENDPOINT, $paymentPublicId), [
            'json' => $this->buildUpdatePayload($data),
        ]);

        return PaymentResponse::fromArray($response);
    }

    /**
     * @param string $paymentPublicId
     * @return PaymentResponse
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $paymentPublicId): PaymentResponse
    {
        $response = $this->client->request('GET', sprintf('%s/%s', self::PAYMENTS_ENDPOINT, $paymentPublicId));

        return PaymentResponse::fromArray($response);
    }

    /**
     * @param string $paymentPublicId
     * @param array $data
     * @return void
     * @throws ApiException
     * @throws GuzzleException
     */
    public function refund(string $paymentPublicId, array $data): void
    {
        $this->client->request('POST', sprintf('%s/%s/refund', self::PAYMENTS_ENDPOINT, $paymentPublicId), [
            'json' => $this->buildRefundPayload($data),
        ]);
    }

    /**
     * POST /payments/{publicId}/pix/regenerate
     *
     * @param string $paymentPublicId
     * @return PaymentResponse
     * @throws ApiException
     * @throws GuzzleException
     */
    public function regeneratePix(string $paymentPublicId): PaymentResponse
    {
        $response = $this->client->request(
            'POST',
            sprintf('%s/%s/pix/regenerate', self::PAYMENTS_ENDPOINT, $paymentPublicId),
        );

        return PaymentResponse::fromArray($response);
    }

    private function buildUpdatePayload(array $data): array
    {
        $payload = [
            'interest_percent' => $data['interest_percent'] ?? null,
            'fine_type' => $data['fine_type'] ?? null,
            'fine_value' => $data['fine_value'] ?? null,
            'discount_type' => $data['discount_type'] ?? null,
            'discount_value' => $data['discount_value'] ?? null,
            'discount_deadline' => $data['discount_deadline'] ?? null,
            'description' => $data['description'] ?? null,
        ];

        return array_filter($payload, static fn($value) => $value !== null);
    }

    private function buildRefundPayload(array $data): array
    {
        $payload = [
            'reason' => $data['reason'] ?? null,
        ];

        return array_filter($payload, static fn($value) => $value !== null);
    }
}
