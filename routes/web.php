<?php

if (Neo\EarlyAccess\Facades\EarlyAccess::isEnabled()) {
    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

    $basePath = config('early-access.url');

    Route::get("{$basePath}/share", 'EarlyAccessController@shareOnTwitter')
        ->name('early-access.share');

    Route::get($basePath, 'EarlyAccessController@index')
        ->name('early-access.index');

    Route::post($basePath, 'EarlyAccessController@subscribe')
        ->name('early-access.subscribe');

    Route::delete($basePath, 'EarlyAccessController@unsubscribe')
        ->name('early-access.unsubscribe');
}
