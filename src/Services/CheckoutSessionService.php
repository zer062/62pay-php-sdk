<?php
declare(strict_types=1);

namespace Sixtytwopay\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sixtytwopay\Client;
use Sixtytwopay\Exceptions\ApiException;
use Sixtytwopay\Inputs\CheckoutSession\CheckoutSessionCreateInput;
use Sixtytwopay\Responses\CheckoutSessionResponse;

final class CheckoutSessionService
{
    private const ENDPOINT = 'checkout-sessions';

    public function __construct(private Client $client)
    {
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(CheckoutSessionCreateInput $input): CheckoutSessionResponse
    {
        $raw = $this->client->request('POST', self::ENDPOINT, ['json' => $input->toPayload()]);

        return CheckoutSessionResponse::fromArray($raw);
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function show(string $id): CheckoutSessionResponse
    {
        $raw = $this->client->request('GET', self::ENDPOINT . '/' . rawurlencode($id));

        return CheckoutSessionResponse::fromArray($raw);
    }
}
