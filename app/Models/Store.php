<?php

namespace App\Models;

use App\Enums\ScraperService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * @property string $name
 * @property string $initials
 * @property array $domains
 * @property HtmlString $domains_html
 * @property array $scrape_strategy
 * @property array $settings
 * @property string $scraper_service
 * @property array $scraper_options
 */
class Store extends Model
{
    /** @use HasFactory<\Database\Factories\StoreFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'initials',
        'domains',
        'scrape_strategy',
        'settings',
    ];

    public array $testResults = [];

    protected function casts(): array
    {
        return [
            'domains' => 'array',
            'scrape_strategy' => 'array',
            'settings' => 'array',
        ];
    }

    /***************************************************
     * Relationships.
     **************************************************/

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function urls(): HasMany
    {
        return $this->hasMany(Url::class);
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(
            Product::class,
            Url::class,
            'store_id',
            'id',
            'id',
            'product_id'
        );
    }

    /***************************************************
     * Attributes.
     **************************************************/

    public function initials(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (! empty($value)) {
                    return strtoupper($value);
                }

                $parts = explode(' ', Str::slug($this->name, ' '));
                $initials = count($parts) > 1
                    ? collect(explode(' ', Str::slug($this->name, ' ')))
                        ->map(fn ($part) => Str::substr($part, 0, 1))
                        ->take(2)->join('')
                    : Str::substr($this->name, 0, 2);

                return strtoupper($initials);
            }
        );
    }

    public function domainsHtml(): Attribute
    {
        return Attribute::make(
            get: fn () => new HtmlString(Str::limit(collect($this->domains)
                ->pluck('domain')
                ->join(', ')))
        );
    }

    public function scraperService(): Attribute
    {
        return Attribute::make(
            get: function () {
                return data_get($this->settings, 'scraper_service', ScraperService::Http->value);
            }
        );
    }

    public function scraperOptions(): Attribute
    {
        return Attribute::make(
            get: function () {
                return collect(explode(PHP_EOL, data_get($this->settings, 'scraper_service_settings', '')))
                    ->filter(fn ($option) => ! empty($option) && Str::contains($option, '='))
                    ->mapWithKeys(function ($option) {
                        $parts = explode('=', $option);

                        return [data_get($parts, 0) => data_get($parts, 1)];
                    })
                    ->toArray();
            }
        );
    }
}
