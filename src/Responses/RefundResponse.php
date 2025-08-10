<?php
declare(strict_types=1);

namespace Sixtytwopay\Responses;

final class RefundResponse
{
    /**
     * @param string $id
     * @param string $status
     * @param int $amount
     * @param string|null $reason
     * @param string|null $denialReason
     * @param string|null $endToEndId
     * @param string|null $refundedAt
     */
    private function __construct(
        private string  $id,
        private string  $status,
        private int     $amount,
        private ?string $reason,
        private ?string $denialReason,
        private ?string $endToEndId,
        private ?string $refundedAt
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
     * @return int
     */
    public function amount(): int
    {
        return $this->amount;
    }

    /**
     * @return string|null
     */
    public function reason(): ?string
    {
        return $this->reason;
    }

    /**
     * @return string|null
     */
    public function denialReason(): ?string
    {
        return $this->denialReason;
    }

    /**
     * @return string|null
     */
    public function endToEndId(): ?string
    {
        return $this->endToEndId;
    }

    /**
     * @return string|null
     */
    public function refundedAt(): ?string
    {
        return $this->refundedAt;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            status: $data['status'],
            amount: $data['amount'],
            reason: $data['reason'] ?? null,
            denialReason: $data['denial_reason'] ?? null,
            endToEndId: $data['end_to_end_id'] ?? null,
            refundedAt: $data['refunded_at'] ?? null
        );
    }
}
