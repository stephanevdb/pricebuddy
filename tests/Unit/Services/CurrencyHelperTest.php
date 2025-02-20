<?php

namespace Tests\Unit\Services;

use App\Services\Helpers\CurrencyHelper;
use Tests\TestCase;

class CurrencyHelperTest extends TestCase
{
    public function test_get_locale_returns_default_locale()
    {
        $this->assertEquals('en_US', CurrencyHelper::getLocale());
    }

    public function test_get_locale_returns_configured_locale()
    {
        config(['app.currency_locale' => 'fr_FR']);
        $this->assertEquals('fr_FR', CurrencyHelper::getLocale());
    }

    public function test_get_all_currencies()
    {
        foreach (CurrencyHelper::getAllCurrencies() as $currency) {
            $this->assertArrayHasKey('country_territory', $currency);
            $this->assertArrayHasKey('currency', $currency);
            $this->assertArrayHasKey('iso', $currency);
            $this->assertArrayHasKey('locale', $currency);
            $this->assertArrayHasKey('separation', $currency);
            $this->assertArrayHasKey('position', $currency);
        }

        config(['app.currency_locale' => 'en_AU']);
        $this->assertSame('AUD', CurrencyHelper::getCurrency());
    }

    public function test_get_currency_iso()
    {
        config(['app.currency_locale' => 'en_AU']);
        $this->assertSame('AUD', CurrencyHelper::getCurrency());
    }

    public function test_get_symbol_returns_correct_symbol()
    {
        config(['app.currency_locale' => 'en_US']);
        $this->assertEquals('$', CurrencyHelper::getSymbol());
    }

    public function test_get_symbol_handles_different_locale()
    {
        $this->assertEquals('€', CurrencyHelper::getSymbol('fr_FR'));
    }

    public function test_to_float_converts_float_value()
    {
        $this->assertEquals(10.5, CurrencyHelper::toFloat(10.5));
    }

    public function test_to_float_converts_int_value()
    {
        $this->assertEquals(10.0, CurrencyHelper::toFloat(10));
    }

    public function test_to_float_converts_string_value()
    {
        $this->assertEquals(10.5, CurrencyHelper::toFloat('10.5'));
    }

    public function test_to_float_handles_non_numeric_string()
    {
        $this->assertEquals(0.0, CurrencyHelper::toFloat('abc'));
    }

    public function test_to_string_formats_float_value()
    {
        $this->assertEquals('$10.50', CurrencyHelper::toString(10.5));
    }

    public function test_to_string_formats_int_value()
    {
        $this->assertEquals('$10.00', CurrencyHelper::toString(10));
    }

    public function test_to_string_formats_string_value()
    {
        $this->assertEquals('$10.50', CurrencyHelper::toString('10.5'));
    }
}
