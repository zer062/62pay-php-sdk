<?php

declare(strict_types=1);

namespace Sixtytwopay;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Sixtytwopay\Exceptions\ApiException;

final class Client
{
    private const string SANDBOX_URL = 'https://sandbox.62pay.com.br/api/v1/';
    private const string PRODUCTION_URL = 'https://62pay.com.br/api/v1/';

    private string $apiKey;
    private string $environment {
        get {
            return $this->environment;
        }
    }

    private GuzzleClient $http;

    public function __construct(string $apiKey, string $environment = 'sandbox')
    {
        $this->apiKey = $apiKey;
        $this->environment = $environment;

        $baseUri = $this->environment === 'production' ? self::PRODUCTION_URL : self::SANDBOX_URL;

        $this->http = new GuzzleClient([
            'base_uri' => $baseUri,
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'timeout' => 10,
        ]);
    }

    /**
     * Send a request to the API.
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param string $uri API endpoint URI (without base url, e.g. "customers")
     * @param array $options Guzzle request options (json, query, etc)
     * @return array Decoded JSON response
     *
     * @throws ApiException on HTTP or connection errors
     */
    public function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->http->request($method, $uri, $options);
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
                $status = $response->getStatusCode();
                $body = (string) $response->getBody();
                $jsonBody = json_decode($body, true);

                // Use ApiException factory to create meaningful exceptions
                throw ApiException::fromHttpResponse($status, $jsonBody, $e);
            }

            // Unexpected error
            throw ApiException::unexpected($e);
        }
    }

}
