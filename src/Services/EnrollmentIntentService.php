<?php
declare(strict_types=1);

namespace Sixtytwopay\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sixtytwopay\Client;
use Sixtytwopay\Exceptions\ApiException;
use Sixtytwopay\Inputs\EnrollmentIntent\EnrollmentIntentCreateInput;
use Sixtytwopay\Responses\EnrollmentIntentResponse;

final class EnrollmentIntentService
{
    private const ENDPOINT = 'enrollment-intents';

    public function __construct(private Client $client)
    {
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(EnrollmentIntentCreateInput $input): EnrollmentIntentResponse
    {
        $raw = $this->client->request('POST', self::ENDPOINT, ['json' => $input->toPayload()]);

        return EnrollmentIntentResponse::fromArray($raw);
    }
}
