<?php

declare(strict_types=1);

namespace Sixtytwopay;

use Sixtytwopay\Services\CustomerService;

final class Sixtytwopay
{
    protected Client $client;

    public function __construct(string $apiKey, string $baseUrl)
    {
        $this->client = new Client($apiKey, $baseUrl);
    }

    public function customer(): CustomerService
    {
        return new CustomerService($this->client);
    }
}
