<?php
declare(strict_types=1);

namespace Sixtytwopay\Responses;

/**
 * @template TItem
 */
final class PaginatedResponse
{
    /** @var array<int,TItem> */
    private array $items;

    private int $currentPage;
    private int $perPage;
    private int $lastPage;
    private int $total;

    /** @var array<string,mixed> */
    private array $meta;

    /** @var array<string,mixed> */
    private array $links;

    /**
     * @param array<int,TItem> $items
     * @param array<string,mixed> $meta
     * @param array<string,mixed> $links
     */
    private function __construct(
        array $items,
        int   $currentPage,
        int   $perPage,
        int   $lastPage,
        int   $total,
        array $meta,
        array $links
    )
    {
        $this->items = $items;
        $this->currentPage = $currentPage;
        $this->perPage = $perPage;
        $this->lastPage = $lastPage;
        $this->total = $total;
        $this->meta = $meta;
        $this->links = $links;
    }

    /**
     * @param array $payload
     * @param callable $itemMapper
     * @return self
     */
    public static function fromArray(array $payload, callable $itemMapper): self
    {
        $data = isset($payload['data']) && is_array($payload['data']) ? $payload['data'] : [];
        $links = isset($payload['links']) && is_array($payload['links']) ? $payload['links'] : [];
        $meta = isset($payload['meta']) && is_array($payload['meta']) ? $payload['meta'] : [];
        $currentPage = (int)($meta['current_page'] ?? $payload['current_page'] ?? 1);
        $perPage = (int)($meta['per_page'] ?? $payload['per_page'] ?? count($data));
        $lastPage = (int)($meta['last_page'] ?? $payload['last_page'] ?? 1);
        $total = (int)($meta['total'] ?? $payload['total'] ?? count($data));

        $items = array_map(static fn(array $row) => $itemMapper($row), $data);

        return new self($items, $currentPage, $perPage, $lastPage, $total, $meta, $links);
    }

    /** @return array<int,TItem> */
    public function items(): array
    {
        return $this->items;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function lastPage(): int
    {
        return $this->lastPage;
    }

    public function total(): int
    {
        return $this->total;
    }

    /** @return array<string,mixed> */
    public function meta(): array
    {
        return $this->meta;
    }

    /** @return array<string,mixed> */
    public function links(): array
    {
        return $this->links;
    }
}
