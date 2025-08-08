<?php

declare(strict_types=1);

namespace Sixtytwopay;

use Sixtytwopay\Services\CustomerService;

final class Sixtytwopay
{
    protected Client $client;

    private const array ALLOWED_ENVIRONMENTS = ['SANDBOX', 'PRODUCTION'];
    private const string DEFAULT_ENVIRONMENT = 'SANDBOX';

    /**
     * @param string $apiKey
     * @param string $environment 'SANDBOX' or 'PRODUCTION' (default 'SANDBOX')
     */
    public function __construct(string $apiKey, string $environment = self::DEFAULT_ENVIRONMENT)
    {
        $environment = strtoupper($environment);
        $this->validateEnvironment($environment);

        $this->client = new Client($apiKey, $environment);
    }

    /**
     * @return CustomerService
     */
    public function customer(): CustomerService
    {
        return new CustomerService($this->client);
    }

    /**
     * @param string $environment
     * @return void
     */
    private function validateEnvironment(string $environment): void
    {
        if (!in_array($environment, self::ALLOWED_ENVIRONMENTS, true)) {
            throw new \InvalidArgumentException($this->getInvalidEnvironmentMessage($environment));
        }
    }

    /**
     * @param string $environment
     * @return string
     */
    private function getInvalidEnvironmentMessage(string $environment): string
    {
        $allowed = implode('", "', self::ALLOWED_ENVIRONMENTS);
        $suggestion = $this->findClosestEnvironment($environment);

        $message = sprintf(
            'Invalid environment "%s". Allowed values are: "%s". Default is "%s".',
            $environment,
            $allowed,
            self::DEFAULT_ENVIRONMENT
        );

        if ($suggestion !== null) {
            $message .= sprintf(' Did you mean "%s"?', $suggestion);
        }

        return $message;
    }

    /**
     * @param string $environment
     * @return string|null
     */
    private function findClosestEnvironment(string $environment): ?string
    {
        return array_find(self::ALLOWED_ENVIRONMENTS, fn($env) => stripos($env, $environment) !== false || stripos($environment, $env) !== false);
    }
}
