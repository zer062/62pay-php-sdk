<?php
declare(strict_types=1);

namespace Sixtytwopay\Inputs\Invoice;


final class InvoiceUpdateInput
{
    /**
     * @param array $fields
     */
    private function __construct(private array $fields)
    {
    }

    /**
     * @param array $d
     * @return self
     */
    public static function fromArray(array $d): self
    {
        $allowed = [
            'payment_method', 'amount', 'due_date', 'description',
            'installments', 'immutable', 'tags',
        ];

        $payload = array_intersect_key($d, array_flip($allowed));

        return new self($payload);
    }

    /**
     * @return array
     */
    public function toPayload(): array
    {
        return array_filter($this->fields, static fn($v) => $v !== null);
    }
}
