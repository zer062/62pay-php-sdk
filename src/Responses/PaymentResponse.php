<?php
declare(strict_types=1);

namespace Sixtytwopay\Responses;

final class PaymentResponse
{
    /**
     * @param string $id
     * @param string $status
     * @param int $amount
     * @param int $originalAmount
     * @param int|null $receivableAmount
     * @param int|null $fees
     * @param float|null $rates
     * @param int|null $interestPercent
     * @param string|null $fineType
     * @param int|null $fineValue
     * @param string|null $discountType
     * @param int|null $discountValue
     * @param string|null $discountDeadline
     * @param string|null $description
     * @param string $paymentMethod
     * @param string|null $dueDate
     * @param string|null $estimatedCreditDate
     * @param string|null $creditDate
     * @param string|null $paidAt
     * @param string|null $receivedAt
     * @param CreditCardPayableResponse|BankSlipPayableResponse|PixPayableResponse|null $payable
     * @param RefundResponse|null $refund
     */
    private function __construct(
        private string                                                                    $id,
        private string                                                                    $status,
        private int                                                                       $amount,
        private int                                                                       $originalAmount,
        private ?int                                                                      $receivableAmount,
        private ?int                                                                      $fees,
        private ?float                                                                    $rates,
        private ?int                                                                      $interestPercent,
        private ?string                                                                   $fineType,
        private ?int                                                                      $fineValue,
        private ?string                                                                   $discountType,
        private ?int                                                                      $discountValue,
        private ?string                                                                   $discountDeadline,
        private ?string                                                                   $description,
        private string                                                                    $paymentMethod,
        private ?string                                                                   $dueDate,
        private ?string                                                                   $estimatedCreditDate,
        private ?string                                                                   $creditDate,
        private ?string                                                                   $paidAt,
        private ?string                                                                   $receivedAt,
        private CreditCardPayableResponse|BankSlipPayableResponse|PixPayableResponse|null $payable,
        private ?RefundResponse                                                           $refund
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
    public function fees(): ?int
    {
        return $this->fees;
    }

    /**
     * @return float|null
     */
    public function rates(): ?float
    {
        return $this->rates;
    }

    /**
     * @return int|null
     */
    public function interestPercent(): ?int
    {
        return $this->interestPercent;
    }

    /**
     * @return string|null
     */
    public function fineType(): ?string
    {
        return $this->fineType;
    }

    /**
     * @return int|null
     */
    public function fineValue(): ?int
    {
        return $this->fineValue;
    }

    /**
     * @return string|null
     */
    public function discountType(): ?string
    {
        return $this->discountType;
    }

    /**
     * @return int|null
     */
    public function discountValue(): ?int
    {
        return $this->discountValue;
    }

    /**
     * @return string|null
     */
    public function discountDeadline(): ?string
    {
        return $this->discountDeadline;
    }

    /**
     * @return string|null
     */
    public function description(): ?string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function paymentMethod(): string
    {
        return $this->paymentMethod;
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
    public function estimatedCreditDate(): ?string
    {
        return $this->estimatedCreditDate;
    }

    /**
     * @return string|null
     */
    public function creditDate(): ?string
    {
        return $this->creditDate;
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
     * @return CreditCardPayableResponse|BankSlipPayableResponse|PixPayableResponse|null
     */
    public function payable(): CreditCardPayableResponse|BankSlipPayableResponse|PixPayableResponse|null
    {
        return $this->payable;
    }

    /**
     * @return RefundResponse|null
     */
    public function refund(): ?RefundResponse
    {
        return $this->refund;
    }

    /**
     * @param array $response
     * @return self
     */
    public static function fromArray(array $response): self
    {
        $data = $response['data'] ?? $response;

        $payable = null;
        if (isset($data['payable']) && is_array($data['payable'])) {
            if (array_key_exists('brand', $data['payable']) && array_key_exists('last_four_digits', $data['payable'])) {
                $payable = CreditCardPayableResponse::fromArray($data['payable']);
            } elseif (array_key_exists('bank_slip_url', $data['payable'])) {
                $payable = BankSlipPayableResponse::fromArray($data['payable']);
            } elseif (array_key_exists('qr_code_base64', $data['payable']) || array_key_exists('copy_paste', $data['payable'])) {
                $payable = PixPayableResponse::fromArray($data['payable']);
            }
        }

        $refund = null;
        if (isset($data['refund']) && is_array($data['refund'])) {
            $refund = RefundResponse::fromArray($data['refund']);
        }

        return new self(
            id: $data['id'],
            status: $data['status'],
            amount: $data['amount'],
            originalAmount: $data['original_amount'],
            receivableAmount: $data['receivable_amount'] ?? null,
            fees: (int)$data['fees'] ?? null,
            rates: (float)$data['rates'] ?? null,
            interestPercent: $data['interest_percent'] ?? null,
            fineType: $data['fine_type'] ?? null,
            fineValue: $data['fine_value'] ?? null,
            discountType: $data['discount_type'] ?? null,
            discountValue: $data['discount_value'] ?? null,
            discountDeadline: $data['discount_deadline'] ?? null,
            description: $data['description'] ?? null,
            paymentMethod: $data['payment_method'],
            dueDate: $data['due_date'] ?? null,
            estimatedCreditDate: $data['estimated_credit_date'] ?? null,
            creditDate: $data['credit_date'] ?? null,
            paidAt: $data['paid_at'] ?? null,
            receivedAt: $data['received_at'] ?? null,
            payable: $payable,
            refund: $refund
        );
    }
}
