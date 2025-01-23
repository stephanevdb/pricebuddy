<?php

namespace App\Rules;

use App\Services\ScrapeUrl;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $store = ScrapeUrl::new($value)->getStore();

        if (empty($value) || ! $store) {
            $fail('The domain does not belong to any stores');
        }

        if ($store) {
            $scrape = ScrapeUrl::new($value)->scrape();

            if (empty($scrape['title'])) {
                $fail('The url does not contain a valid title');
            }
        }
    }
}
