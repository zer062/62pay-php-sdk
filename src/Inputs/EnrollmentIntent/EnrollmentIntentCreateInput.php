<?php
declare(strict_types=1);

namespace Sixtytwopay\Inputs\EnrollmentIntent;

final class EnrollmentIntentCreateInput
{
    private function __construct(private array $fields)
    {
    }

    public static function fromArray(array $data): self
    {
        $allowed = [
            'name', 'phone', 'email', 'whatsapp_opt_in', 'capture_point',
            'course_name', 'course_external_id', 'amount', 'behavior', 'attribution',
        ];

        $fields = array_intersect_key($data, array_flip($allowed));

        if (array_key_exists('whatsapp_opt_in', $fields)) {
            $fields['whatsapp_opt_in'] = (bool) $fields['whatsapp_opt_in'];
        }

        return new self($fields);
    }

    public function toPayload(): array
    {
        return array_filter($this->fields, static fn($v) => $v !== null && $v !== '');
    }
}
