<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::resource('products', ProductController::class);

Route::post('products/list', [ProductController::class, 'productList'])->name('product.list.ajax');


Route::post('products/image/remove', [ProductImageController::class, 'removeImage'])->name('product.image.remove');
