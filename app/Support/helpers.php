<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('currency_symbol')) {
    function currency_symbol(): string
    {
        return (string) setting('currency_symbol', '₦');
    }
}

if (! function_exists('format_money')) {
    function format_money(float|int|string|null $amount): string
    {
        return currency_symbol().number_format((float) $amount, 2);
    }
}

if (! function_exists('format_date')) {
    function format_date(string|\DateTimeInterface|null $date): string
    {
        if ($date === null) {
            return '—';
        }

        if (is_string($date)) {
            $date = \Illuminate\Support\Carbon::parse($date);
        }

        return $date->format('d/m/Y');
    }
}

if (! function_exists('format_datetime')) {
    function format_datetime(string|\DateTimeInterface|null $date): string
    {
        if ($date === null) {
            return '—';
        }

        if (is_string($date)) {
            $date = \Illuminate\Support\Carbon::parse($date);
        }

        return $date->format('d/m/Y H:i');
    }
}