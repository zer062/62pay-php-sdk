<?php

declare(strict_types=1);

namespace Sixtytwopay;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Sixtytwopay\Exceptions\ApiException;

final class Client
{
    private const string SANDBOX_URL = 'https://sandbox.62pay.com.br/api/v1/';
    private const string PRODUCTION_URL = 'https://62pay.com.br/api/v1/';

    private readonly string $apiKey;
    private readonly string $environment;
    private GuzzleClient $http;

    /**
     * @param string $apiKey
     * @param string $environment 'SANDBOX' or 'PRODUCTION'
     * @param array $guzzleOptions Additional Guzzle client options
     */
    public function __construct(string $apiKey, string $environment = 'SANDBOX', array $guzzleOptions = [])
    {
        $this->apiKey = $apiKey;
        $this->environment = $environment;

        $baseUri = $environment === 'PRODUCTION' ? self::PRODUCTION_URL : self::SANDBOX_URL;

        $defaultOptions = [
            'base_uri' => $baseUri,
            'headers' => [
                'Authorization' => "Bearer {$apiKey}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'timeout' => 10,
        ];

        $this->http = new GuzzleClient(array_merge($defaultOptions, $guzzleOptions));
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return array
     * @throws ApiException
     * @throws GuzzleException
     */
    public function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->http->request($method, $uri, $options);

            $statusCode = $response->getStatusCode();
            if ($statusCode < 200 || $statusCode >= 300) {
                $body = (string) $response->getBody();
                $jsonBody = json_decode($body, true);
                throw ApiException::fromHttpResponse($statusCode, $jsonBody);
            }

            $body = (string) $response->getBody();
            $decoded = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ApiException('Invalid JSON response: ' . json_last_error_msg());
            }

            return $decoded ?? [];
        } catch (ConnectException $e) {
            throw ApiException::connection($e);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            if ($response) {
                $statusCode = $response->getStatusCode();
                $body = (string) $response->getBody();
                $jsonBody = json_decode($body, true);
                throw ApiException::fromHttpResponse($statusCode, $jsonBody, $e);
            }
            throw ApiException::unexpected($e);
        }
    }

    /**
     * @param string $uri
     * @param array $options
     * @return array
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $uri, array $options = []): array
    {
        return $this->request('GET', $uri, $options);
    }

    /**
     * @param string $uri
     * @param array $options
     * @return array
     * @throws ApiException
     * @throws GuzzleException
     */
    public function post(string $uri, array $options = []): array
    {
        return $this->request('POST', $uri, $options);
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }
}
