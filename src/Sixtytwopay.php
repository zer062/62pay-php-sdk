<?php

declare(strict_types=1);

namespace Sixtytwopay;

use Sixtytwopay\Services\CustomerService;

final class Sixtytwopay
{
    protected Client $client;

    /**
     * @param string $apiKey
     * @param string $environment 'SANDBOX' or 'PRODUCTION' (default 'SANDBOX')
     */
    public function __construct(string $apiKey, string $environment = 'SANDBOX')
    {
        $allowedEnvironments = ['SANDBOX', 'PRODUCTION'];
        if (!in_array($environment, $allowedEnvironments, true)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid environment "%s". Allowed values: %s',
                $environment,
                implode(', ', $allowedEnvironments)
            ));
        }

        $this->client = new Client($apiKey, $environment);
    }

    /**
     * @return CustomerService
     */
    public function customer(): CustomerService
    {
        return new CustomerService($this->client);
    }
}
