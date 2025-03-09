<?php

namespace App\Dto;

use App\Enums\Trend;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;

class PriceCacheDto
{
    private ?int $storeId;

    private ?string $storeName;

    private ?int $urlId;

    private ?string $url;

    private string $trend;

    private float $price;

    private array $history;

    private ?Carbon $lastScrapeDate;

    public function __construct(
        float $price,
        ?int $storeId = null,
        ?string $storeName = null,
        ?int $urlId = null,
        ?string $url = null,
        string $trend = Trend::None->value,
        array $history = [],
        ?string $lastScrape = null
    ) {
        $this->storeId = $storeId;
        $this->storeName = $storeName;
        $this->urlId = $urlId;
        $this->url = $url;
        $this->trend = $trend;
        $this->price = $price;
        $this->history = $history;
        $this->lastScrapeDate = $lastScrape ? Carbon::parse($lastScrape) : null;
    }

    // Getters
    public function getStoreId(): ?int
    {
        return $this->storeId;
    }

    public function getStoreName(): ?string
    {
        return $this->storeName;
    }

    public function getUrlId(): ?int
    {
        return $this->urlId;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getTrend(): string
    {
        return $this->trend;
    }

    public function getTrendColor(): string
    {
        return Trend::getColor($this->getTrend());
    }

    public function getTrendIcon(): string
    {
        return Trend::getIcon($this->getTrend());
    }

    public function getTrendText(): string
    {
        return Trend::getText($this->getTrend());
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getPriceFormatted(): string
    {
        return Number::currency($this->getPrice());
    }

    public function getHistory(int $count = 365): Collection
    {
        return collect($this->history)->reverse()->take($count)->reverse();
    }

    public function getAggregateFormatted(): array
    {
        $history = $this->getHistory();

        return [
            'avg' => Number::currency($history->avg()),
            'min' => Number::currency($history->min()),
            'max' => Number::currency($history->max()),
        ];
    }

    public function getLastScrapeDate(): ?Carbon
    {
        return $this->lastScrapeDate;
    }

    public function getHoursSinceLastScrape(): ?float
    {
        return $this->lastScrapeDate?->diffInHours(now());
    }

    public function isLastScrapeSuccessful(): bool
    {
        $hours = $this->getHoursSinceLastScrape();

        return $hours && $hours < 24;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['price'],
            $data['store_id'] ?? null,
            $data['store_name'] ?? 'Unknown',
            $data['url_id'] ?? null,
            $data['url'] ?? null,
            $data['trend'] ?? Trend::None->value,
            $data['history'],
            $data['last_scrape'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'store_id' => $this->getStoreId(),
            'store_name' => $this->getStoreName(),
            'url_id' => $this->getUrlId(),
            'url' => $this->getUrl(),
            'trend' => $this->getTrend(),
            'trend_color' => $this->getTrendColor(),
            'trend_icon' => $this->getTrendIcon(),
            'trend_text' => $this->getTrendText(),
            'price' => $this->getPrice(),
            'price_formatted' => $this->getPriceFormatted(),
            'history' => $this->getHistory(),
            'last_scrape' => $this->getLastScrapeDate(),
            'hours_since_last_scrape' => $this->getHoursSinceLastScrape(),
            'successful_last_scrape' => $this->isLastScrapeSuccessful(),
        ];
    }
}
