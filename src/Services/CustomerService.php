<?php

declare(strict_types=1);

namespace Sixtytwopay\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sixtytwopay\Client;
use Sixtytwopay\Exceptions\ApiException;
use Sixtytwopay\Inputs\Customer\CustomerCreateInput;
use Sixtytwopay\Inputs\Customer\CustomerUpdateInput;
use Sixtytwopay\Responses\CustomerResponse;

final class CustomerService
{
    private const CUSTOMER_ENDPOINT = 'customers';

    /**
     * @param Client $client
     */
    public function __construct(private Client $client)
    {
    }

    /**
     * @param CustomerCreateInput $input
     * @return CustomerResponse
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(CustomerCreateInput $input): CustomerResponse
    {
        $raw = $this->client->request('POST', self::CUSTOMER_ENDPOINT, [
            'json' => $input->toPayload(),
        ]);

        return CustomerResponse::fromArray($raw);
    }

    /**
     * @param string $customer
     * @param CustomerUpdateInput $input
     * @return CustomerResponse
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(string $customer, CustomerUpdateInput $input): CustomerResponse
    {
        $raw = $this->client->request('PATCH', sprintf('%s/%s', self::CUSTOMER_ENDPOINT, $customer), [
            'json' => $input->toPayload(),
        ]);

        return CustomerResponse::fromArray($raw);
    }

    /**
     * @param string $customer
     * @return void
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(string $customer): void
    {
        $this->client->request('DELETE', sprintf('%s/%s', self::CUSTOMER_ENDPOINT, $customer));
    }

    /**
     * @param string $customer
     * @return CustomerResponse
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $customer): CustomerResponse
    {
        $raw = $this->client->request('GET', sprintf('%s/%s', self::CUSTOMER_ENDPOINT, $customer));
        return CustomerResponse::fromArray($raw);
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
}
