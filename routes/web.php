<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Vanadi\Framework\Models\Country;

Route::middleware('web')->get('countries/{code}/flag', function (Request $request, string $code) {
    $country = Country::whereCode(strtoupper($code))->first();
    if (! $country) {
        return null;
    }
    $contents = file_get_contents(base_path($country->flag_svg_path));

    return response($contents)->header('Content-Type', 'image/svg+xml');
})->name('countries.code.flag');
