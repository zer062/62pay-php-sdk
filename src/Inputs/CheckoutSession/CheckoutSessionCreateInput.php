<?php
declare(strict_types=1);

namespace Sixtytwopay\Inputs\CheckoutSession;

final class CheckoutSessionCreateInput
{
    private function __construct(private array $fields)
    {
    }

    public static function fromArray(array $data): self
    {
        $allowed = [
            'amount', 'description', 'category', 'course_name', 'course_external_id',
            'external_reference', 'success_url', 'cancel_url', 'expires_in_minutes',
            'payment_options', 'attribution',
        ];

        return new self(array_intersect_key($data, array_flip($allowed)));
    }

    public function toPayload(): array
    {
        return array_filter($this->fields, static fn($v) => $v !== null && $v !== '');
    }
}
