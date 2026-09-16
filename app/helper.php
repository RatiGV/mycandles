<?php

use App\Models\Information;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


if (!function_exists('locale')) {
    function locale()
    {
        return LaravelLocalization::getCurrentLocale();
    }
}

if (!function_exists('information')) {
    function information()
    {
        $info = Information::with('translate')->first();

        return $info;
    }
}
if (!function_exists('shorten')) {
    function shorten($string, $limit = 100)
    {
        $string = trim($string);

        if (strlen($string) <= $limit) {
            return $string;
        }

        return substr($string, 0, $limit) . '...';
    }
}
if (!function_exists('getPrice')) {
    function getPrice($price)
    {
        return number_format($price, 2, '.', ',');
    }
}
