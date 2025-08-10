<?php

declare(strict_types=1);

namespace Sixtytwopay\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sixtytwopay\Client;
use Sixtytwopay\Exceptions\ApiException;
use Sixtytwopay\Inputs\Checkout\CheckoutCreditCardInput;
use Sixtytwopay\Responses\InvoiceResponse;

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
     * @param CheckoutCreditCardInput $input
     * @return InvoiceResponse
     * @throws ApiException
     * @throws GuzzleException
     */
    public function payWithCreditCard(string $invoiceId, CheckoutCreditCardInput $input): InvoiceResponse
    {
        $raw = $this->client->request('POST', sprintf('%s/%s/credit-card', self::CHECKOUT_CREDIT_CARD_ENDPOINT, $invoiceId), [
            'json' => $input->toPayload()
        ]);

        return InvoiceResponse::fromArray($raw);
    }
}
