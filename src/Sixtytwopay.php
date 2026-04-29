<?php

declare(strict_types=1);

namespace Sixtytwopay;

use InvalidArgumentException;
use Sixtytwopay\Services\CustomerService;
use Sixtytwopay\Services\InvoiceService;
use Sixtytwopay\Services\PaymentService;

final class Sixtytwopay
{
    protected Client $client;

    private const ALLOWED_ENVIRONMENTS = ['SANDBOX', 'PRODUCTION'];

    private const DEFAULT_ENVIRONMENT = 'SANDBOX';

    /**
     * @param string $apiKey
     * @param string $environment 'SANDBOX' or 'PRODUCTION'
     */
    public function __construct(string $apiKey, string $environment = self::DEFAULT_ENVIRONMENT)
    {
        $environment = strtoupper($environment);

        $this->validateEnvironment($environment);

        $this->client = new Client($apiKey, $environment);
    }

    public function customer(): CustomerService
    {
        return new CustomerService($this->client);
    }

    public function invoice(): InvoiceService
    {
        return new InvoiceService($this->client);
    }

    public function payment(): PaymentService
    {
        return new PaymentService($this->client);
    }

    private function validateEnvironment(string $environment): void
    {
        if (!in_array($environment, self::ALLOWED_ENVIRONMENTS, true)) {
            throw new InvalidArgumentException($this->getInvalidEnvironmentMessage($environment));
        }
    }

    private function getInvalidEnvironmentMessage(string $environment): string
    {
        $allowed = implode('", "', self::ALLOWED_ENVIRONMENTS);
        $suggestion = $this->findClosestEnvironment($environment);

        $message = sprintf(
            'Invalid environment "%s". Allowed values are: "%s". Default is "%s".',
            $environment,
            $allowed,
            self::DEFAULT_ENVIRONMENT,
        );

        if ($suggestion !== null) {
            $message .= sprintf(' Did you mean "%s"?', $suggestion);
        }

        return $message;
    }

    private function findClosestEnvironment(string $environment): ?string
    {
        foreach (self::ALLOWED_ENVIRONMENTS as $env) {
            if (stripos($env, $environment) !== false || stripos($environment, $env) !== false) {
                return $env;
            }
        }

        return null;
    }
}
