<?php
declare(strict_types=1);

namespace Sixtytwopay\Responses;

final class InvoiceResponse
{
    /**
     * @param string $id
     * @param string $status
     * @param string|null $paymentMethod
     * @param int $amount
     * @param int $originalAmount
     * @param int|null $receivableAmount
     * @param int|null $totalRates
     * @param int|null $installments
     * @param string|null $dueDate
     * @param string|null $paidAt
     * @param string|null $receivedAt
     * @param string|null $description
     * @param bool|null $immutable
     * @param string $checkoutUrl
     * @param PaymentResponse[] $payments
     * @param TagResponse[]|null $tags
     */
    private function __construct(
        private string  $id,
        private string  $status,
        private ?string $paymentMethod,
        private int     $amount,
        private int     $originalAmount,
        private ?int    $receivableAmount,
        private ?int    $totalRates,
        private ?int    $installments,
        private ?string $dueDate,
        private ?string $paidAt,
        private ?string $receivedAt,
        private ?string $description,
        private ?bool   $immutable,
        private string  $checkoutUrl,
        private array   $payments,
        private ?array  $tags,
    )
    {
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function status(): string
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function paymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    /**
     * @return int
     */
    public function amount(): int
    {
        return $this->amount;
    }

    /**
     * @return int
     */
    public function originalAmount(): int
    {
        return $this->originalAmount;
    }

    /**
     * @return int|null
     */
    public function receivableAmount(): ?int
    {
        return $this->receivableAmount;
    }

    /**
     * @return int|null
     */
    public function totalRates(): ?int
    {
        return $this->totalRates;
    }

    /**
     * @return int|null
     */
    public function installments(): ?int
    {
        return $this->installments;
    }

    /**
     * @return string|null
     */
    public function dueDate(): ?string
    {
        return $this->dueDate;
    }

    /**
     * @return string|null
     */
    public function paidAt(): ?string
    {
        return $this->paidAt;
    }

    /**
     * @return string|null
     */
    public function receivedAt(): ?string
    {
        return $this->receivedAt;
    }

    /**
     * @return string|null
     */
    public function description(): ?string
    {
        return $this->description;
    }

    /**
     * @return bool|null
     */
    public function immutable(): ?bool
    {
        return $this->immutable;
    }

    /**
     * @return string
     */
    public function checkoutUrl(): string
    {
        return $this->checkoutUrl;
    }

    /**
     * @return PaymentResponse[]
     */
    public function payments(): array
    {
        return $this->payments;
    }

    /**
     * @return TagResponse[]|null
     */
    public function tags(): ?array
    {
        return $this->tags;
    }

    /**
     * @param array $response
     * @return self
     */
    public static function fromArray(array $response): self
    {
        $data = $response['data'] ?? $response;

        $payments = [];

        if (isset($data['payments']) && is_array($data['payments'])) {
            $payments = array_map(
                static fn(array $p) => PaymentResponse::fromArray($p),
                $data['payments']
            );
        }

        $tags = null;
        if (isset($data['tags']) && is_array($data['tags'])) {
            $tags = array_map(
                static fn(array $t) => TagResponse::fromArray($t),
                $data['tags']
            );
        }

        return new self(
            id: $data['id'],
            status: $data['status'],
            paymentMethod: $data['payment_method'] ?? null,
            amount: $data['amount'],
            originalAmount: $data['original_amount'],
            receivableAmount: $data['receivable_amount'] ?? null,
            totalRates: $data['total_rates'] ?? null,
            installments: $data['installments'] ?? null,
            dueDate: $data['due_date'] ?? null,
            paidAt: $data['paid_at'] ?? null,
            receivedAt: $data['received_at'] ?? null,
            description: $data['description'] ?? null,
            immutable: isset($data['immutable']) ? (bool)$data['immutable'] : null,
            checkoutUrl: $data['checkout_url'],
            payments: $payments,
            tags: $tags,
        );
    }
}
