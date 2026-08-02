<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupShoesController;
use App\Http\Controllers\ModelsShoesController;

// Створюємо ресурсний маршрут для груп товарів
// Запит буде йти на: /rest-api/categories
Route::apiResource('/shoes/groups', GroupShoesController::class);
Route::apiResource('/shoes/models', ModelsShoesController::class);