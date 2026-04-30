<?php

declare(strict_types=1);

namespace Sixtytwopay\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sixtytwopay\Client;
use Sixtytwopay\Exceptions\ApiException;
use Sixtytwopay\Inputs\Invoice\InvoiceCreateInput;
use Sixtytwopay\Inputs\Invoice\InvoiceCreditCardInput;
use Sixtytwopay\Inputs\Invoice\InvoiceUpdateInput;
use Sixtytwopay\Responses\InvoiceCreditCardPaymentResponse;
use Sixtytwopay\Responses\InvoiceResponse;

final class InvoiceService
{
    private const INVOICES_ENDPOINT = 'invoices';

    public function __construct(private Client $client)
    {
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(InvoiceCreateInput $input): InvoiceResponse
    {
        $raw = $this->client->request('POST', self::INVOICES_ENDPOINT, [
            'json' => $input->toPayload(),
        ]);

        return InvoiceResponse::fromArray($raw);
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(string $invoice, InvoiceUpdateInput $input): InvoiceResponse
    {
        $raw = $this->client->request('PUT', sprintf('%s/%s', self::INVOICES_ENDPOINT, $invoice), [
            'json' => $input->toPayload(),
        ]);

        return InvoiceResponse::fromArray($raw);
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(string $invoice): void
    {
        $this->client->request('DELETE', sprintf('%s/%s', self::INVOICES_ENDPOINT, $invoice));
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $invoice): InvoiceResponse
    {
        $raw = $this->client->request('GET', sprintf('%s/%s', self::INVOICES_ENDPOINT, $invoice));

        return InvoiceResponse::fromArray($raw);
    }

    /**
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
     * @throws ApiException
     * @throws GuzzleException
     */
    public function refund(string $invoice): void
    {
        $this->client->request('POST', sprintf('%s/%s/refund', self::INVOICES_ENDPOINT, $invoice));
    }

    public function payWithCreditCard(
        string                 $invoice,
        InvoiceCreditCardInput $input,
    ): InvoiceCreditCardPaymentResponse
    {
        try {
            $raw = $this->client->request(
                'POST',
                sprintf('%s/%s/pay/credit-card', self::INVOICES_ENDPOINT, $invoice),
                [
                    'json' => $input->toPayload(),
                ],
            );

            return InvoiceCreditCardPaymentResponse::fromArray($raw);
        } catch (ApiException $exception) {
            return InvoiceCreditCardPaymentResponse::fromApiException($exception);
        }
    }
}
