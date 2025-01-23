<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::prefix('admin/products')->name('filament.admin.resources.products.')
    ->group(function () {
        Route::post('/{product}/fetch', [ProductController::class, 'fetch'])
            ->name('fetch');
    });
