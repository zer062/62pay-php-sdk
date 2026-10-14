<?php
declare(strict_types=1);

namespace Sixtytwopay\Responses;

final class EnrollmentIntentResponse
{
    private function __construct(private string $id)
    {
    }

    public function id(): string
    {
        return $this->id;
    }

    public static function fromArray(array $response): self
    {
        $data = $response['data'] ?? $response;

        return new self((string) $data['id']);
    }
}
