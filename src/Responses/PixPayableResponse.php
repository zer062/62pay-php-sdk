<?php
declare(strict_types=1);

namespace Sixtytwopay\Responses;

final class PixPayableResponse
{
    /**
     * @param string $id
     * @param string|null $qrCodeBase64
     * @param string|null $copyPaste
     * @param string|null $expiresAt
     */
    private function __construct(
        private string  $id,
        private ?string $qrCodeBase64,
        private ?string $copyPaste,
        private ?string $expiresAt
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
     * @return string|null
     */
    public function qrCodeBase64(): ?string
    {
        return $this->qrCodeBase64;
    }

    /**
     * @return string|null
     */
    public function copyPaste(): ?string
    {
        return $this->copyPaste;
    }

    /**
     * @return string|null
     */
    public function expiresAt(): ?string
    {
        return $this->expiresAt;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            qrCodeBase64: $data['qr_code_base64'] ?? null,
            copyPaste: $data['copy_paste'] ?? null,
            expiresAt: $data['expires_at'] ?? null
        );
    }
}
