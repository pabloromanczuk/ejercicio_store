<?php

use Illuminate\Support\Facades\Route;

// Todo el front es Vue (SPA de una sola vista) interactuando por /api/*.
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!api).*$')->name('spa');
