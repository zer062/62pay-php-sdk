<?php
declare(strict_types=1);

namespace Sixtytwopay\Responses;

final class TagResponse
{
    /**
     * @param string $id
     * @param string $name
     * @param string|null $description
     */
    private function __construct(
        private string  $id,
        private string  $name,
        private ?string $description
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
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function description(): ?string
    {
        return $this->description;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            description: $data['description'] ?? null
        );
    }
}
