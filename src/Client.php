<?php

declare(strict_types=1);

namespace Sixtytwopay;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Sixtytwopay\Exceptions\ApiException;
use Throwable;

final class Client
{
    private const SANDBOX_URL = 'https://sandbox.62pay.com.br/api/v1/';
    private const PRODUCTION_URL = 'https://62pay.com.br/api/v1/';

    private string $apiKey;
    private string $environment;
    private GuzzleClient $http;

    public function __construct(string $apiKey, string $environment = 'SANDBOX', array $guzzleOptions = [])
    {
        $this->apiKey = $apiKey;
        $this->environment = $environment;

        $baseUri = $environment === 'PRODUCTION'
            ? self::PRODUCTION_URL
            : self::SANDBOX_URL;

        $defaultOptions = [
            'base_uri' => $baseUri,
            'headers' => [
                'Authorization' => "Bearer {$apiKey}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => sprintf('62pay-php-sdk PHP/%s', PHP_VERSION),
            ],
            'timeout' => 10,
            'http_errors' => false,
        ];

        $this->http = new GuzzleClient(array_replace_recursive($defaultOptions, $guzzleOptions));
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->http->request($method, $uri, $options);

            return $this->handleResponse($response);
        } catch (ConnectException $e) {
            throw ApiException::connection($e);
        } catch (RequestException $e) {
            $this->handleRequestException($e);
        } catch (ApiException $e) {
            throw $e;
        } catch (GuzzleException $e) {
            throw new ApiException(
                message: 'Guzzle error: ' . $e->getMessage(),
                code: 0,
                previous: $e,
            );
        } catch (Throwable $e) {
            throw ApiException::unexpected($e);
        }
    }

    /**
     * @throws ApiException
     */
    private function handleResponse(ResponseInterface $response): array
    {
        $statusCode = $response->getStatusCode();
        $body = (string)$response->getBody();
        $decoded = $this->decodeJson($body);

        if ($statusCode < 200 || $statusCode >= 300) {
            throw ApiException::fromHttpResponse($statusCode, $decoded);
        }

        return $decoded;
    }

    /**
     * @throws ApiException
     */
    private function decodeJson(string $json): array
    {
        if (trim($json) === '') {
            return [];
        }

        $decoded = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException('Invalid JSON response: ' . json_last_error_msg());
        }

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @throws ApiException
     */
    private function handleRequestException(RequestException $e): void
    {
        $response = $e->getResponse();

        if (!$response) {
            throw new ApiException(
                message: 'Request failed without HTTP response: ' . $e->getMessage(),
                code: 0,
                previous: $e,
            );
        }

        $statusCode = $response->getStatusCode();
        $body = (string)$response->getBody();

        try {
            $jsonBody = $this->decodeJson($body);
        } catch (ApiException) {
            $jsonBody = [
                'message' => $body !== '' ? $body : $e->getMessage(),
            ];
        }

        throw ApiException::fromHttpResponse($statusCode, $jsonBody, $e);
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $uri, array $options = []): array
    {
        return $this->request('GET', $uri, $options);
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function post(string $uri, array $options = []): array
    {
        return $this->request('POST', $uri, $options);
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function put(string $uri, array $options = []): array
    {
        return $this->request('PUT', $uri, $options);
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function patch(string $uri, array $options = []): array
    {
        return $this->request('PATCH', $uri, $options);
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(string $uri, array $options = []): array
    {
        return $this->request('DELETE', $uri, $options);
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }
}
