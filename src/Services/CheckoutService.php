<?php

declare(strict_types=1);

namespace Sixtytwopay\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sixtytwopay\Client;
use Sixtytwopay\Exceptions\ApiException;

final class CheckoutService
{
    private const CHECKOUT_CREDIT_CARD_ENDPOINT = 'checkout/pay';

    /**
     * @param Client $client
     */
    public function __construct(private Client $client)
    {
    }

    /**
     * @param string $invoiceId
     * @param array $data
     * @return array
     * @throws ApiException
     * @throws GuzzleException
     */
    public function payWithCreditCard(string $invoiceId, array $data): array
    {
        $payload = $this->buildPayload($data);

        return $this->client->request('POST', sprintf('%s/%s/credit-card', self::CHECKOUT_CREDIT_CARD_ENDPOINT, $invoiceId), [
            'json' => $payload,
        ]);
    }

    /**
     * @param array $data
     * @return array
     */
    private function buildPayload(array $data): array
    {
        $payload = [
            'holder_name' => $data['holder_name'] ?? null,
            'number' => $data['number'] ?? null,
            'card_expiry_date' => $data['card_expiry_date'] ?? null,
            'ccv' => $data['ccv'] ?? null,
            'installments' => $data['installments'] ?? null,
            'billing_name' => $data['billing_name'] ?? null,
            'billing_email' => $data['billing_email'] ?? null,
            'billing_document_number' => $data['billing_document_number'] ?? null,
            'billing_postal_code' => $data['billing_postal_code'] ?? null,
            'billing_address_number' => $data['billing_address_number'] ?? null,
            'billing_address_complement' => $data['billing_address_complement'] ?? null,
            'billing_phone' => $data['billing_phone'] ?? null,
        ];

        return array_filter($payload, fn($value) => $value !== null);
    }
}
