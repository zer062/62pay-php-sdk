<?php
declare(strict_types=1);

namespace Sixtytwopay\Inputs\Invoice;

final class InvoiceCreateInput
{
    /**
     * @param string $customer
     * @param string $paymentMethod
     * @param int $amount
     * @param string $dueDate
     * @param string|null $description
     * @param int|null $installments
     * @param bool|null $immutable
     * @param int|null $interestPercent
     * @param string|null $fineType
     * @param int|null $fineValue
     * @param string|null $discountType
     * @param int|null $discountValue
     * @param string|null $discountDeadline
     * @param array|null $tags
     */
    public function __construct(
        private string  $customer,
        private string  $paymentMethod,
        private int     $amount,
        private string  $dueDate,
        private ?string $description = null,
        private ?int    $installments = 1,
        private ?bool   $immutable = null,
        private ?int    $interestPercent = null,
        private ?string $fineType = null,
        private ?int    $fineValue = null,
        private ?string $discountType = null,
        private ?int    $discountValue = null,
        private ?string $discountDeadline = null,
        private ?array  $tags = null
    )
    {
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            customer: $data['customer'],
            paymentMethod: $data['payment_method'],
            amount: (int)$data['amount'],
            dueDate: (string)$data['due_date'],
            description: $data['description'] ?? null,
            installments: $data['installments'] ?? 1,
            immutable: (bool)$data['immutable'] ?? false,
            interestPercent: $data['interest_percent'] ?? null,
            fineType: $data['fine_type'] ?? null,
            fineValue: $data['fine_value'] ?? null,
            discountType: $data['discount_type'] ?? null,
            discountValue: $data['discount_value'] ?? null,
            discountDeadline: $data['discount_deadline'] ?? null,
            tags: isset($data['tags']) && is_array($data['tags']) ? array_values($data['tags']) : null,
        );
    }

    /**
     * @return array
     */
    public function toPayload(): array
    {
        $payload = [
            'customer' => $this->customer,
            'payment_method' => $this->paymentMethod,
            'amount' => $this->amount,
            'due_date' => $this->dueDate,
            'description' => $this->description,
            'installments' => $this->installments,
            'immutable' => $this->immutable,
            'interest_percent' => $this->interestPercent,
            'fine_type' => $this->fineType,
            'fine_value' => $this->fineValue,
            'discount_type' => $this->discountType,
            'discount_value' => $this->discountValue,
            'discount_deadline' => $this->discountDeadline,
            'tags' => $this->tags,
        ];
        return array_filter($payload, static fn($v) => $v !== null);
    }
}
