<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(route('filament.admin.pages.home-dashboard', [], false));
});

Route::prefix('admin/products')->name('filament.admin.resources.products.')
    ->group(function () {
        Route::post('/{product}/fetch', [ProductController::class, 'fetch'])
            ->name('fetch');
    });
