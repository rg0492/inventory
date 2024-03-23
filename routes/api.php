<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [App\Http\Controllers\API\UserController::class, 'login']);


Route::middleware('jwt.auth')->group(function () {
    Route::get('/product/list', 'App\Http\Controllers\API\ProductController@index');
});