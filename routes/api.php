<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\UserController;

Route::prefix('service-orders')->group(function () {
    Route::get('/', [ServiceOrderController::class, 'index']);
    Route::post('/', [ServiceOrderController::class, 'store']);
    

});

Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);     // Listar todos
    Route::post('/', [UserController::class, 'store']);    // Criar novo
  
});

Route::get('/', function () {
    return "Welcome to the API!";
});