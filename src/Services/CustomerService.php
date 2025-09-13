<?php

declare(strict_types=1);

namespace Sixtytwopay\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sixtytwopay\Client;
use Sixtytwopay\Exceptions\ApiException;
use Sixtytwopay\Inputs\Customer\CustomerCreateInput;
use Sixtytwopay\Inputs\Customer\CustomerUpdateInput;
use Sixtytwopay\Responses\CustomerResponse;
use Sixtytwopay\Responses\InvoiceResponse;
use Sixtytwopay\Responses\PaginatedResponse;

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
     * @return PaginatedResponse
     * @throws ApiException
     * @throws GuzzleException
     */
    public function list(array $filters = []): PaginatedResponse
    {
        $query = $this->sanitizeCustomerListFilters($filters);

        $raw = $this->client->request('GET', self::CUSTOMER_ENDPOINT, [
            'query' => $query,
        ]);

        return PaginatedResponse::fromArray(
            $raw,
            static fn(array $row) => CustomerResponse::fromArray($row)
        );
    }

    /**
     * @param string $customerPublicId
     * @param array $params
     * @return PaginatedResponse
     * @throws ApiException
     * @throws GuzzleException
     */
    public function invoices(string $customerPublicId, array $params = []): PaginatedResponse
    {
        $query = $this->sanitizePaginationParams($params);

        $raw = $this->client->request(
            'GET',
            sprintf('%s/%s/invoices', self::CUSTOMER_ENDPOINT, $customerPublicId),
            ['query' => $query]
        );

        return PaginatedResponse::fromArray(
            $raw,
            static fn(array $row) => InvoiceResponse::fromArray($row)
        );
    }

    /**
     * @param array $filters
     * @return array
     */
    private function sanitizeCustomerListFilters(array $filters): array
    {
        $allowed = ['name', 'email', 'document_number', 'with_trashed', 'page', 'per_page'];

        $clean = array_intersect_key($filters, array_flip($allowed));

        if (isset($clean['name'])) {
            $clean['name'] = trim((string)$clean['name']);
        }
        if (isset($clean['email'])) {
            $clean['email'] = trim((string)$clean['email']);
        }

        if (isset($clean['document_number'])) {
            $clean['document_number'] = $this->onlyDigits((string)$clean['document_number']);
        }

        if (isset($clean['with_trashed'])) {
            $clean['with_trashed'] = filter_var($clean['with_trashed'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
        }

        return array_merge($clean, $this->sanitizePaginationParams($clean));
    }

    /**
     * @param array $params
     * @return array
     */
    private function sanitizePaginationParams(array $params): array
    {
        $out = [];

        if (isset($params['page'])) {
            $out['page'] = max(1, (int)$params['page']);
        }

        if (isset($params['per_page'])) {
            $perPage = (int)$params['per_page'];
            $out['per_page'] = max(1, $perPage);
        }

        return array_replace($params, $out);
    }

    /**
     * @param string $value
     * @return string
     */
    private function onlyDigits(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }
}
