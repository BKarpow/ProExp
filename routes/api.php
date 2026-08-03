<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupShoesController;
use App\Http\Controllers\ModelsShoesController;
use App\Http\Controllers\WarehouseShoesController;

// Створюємо ресурсний маршрут для груп товарів
// Запит буде йти на: /rest-api/categories
Route::apiResource('/shoes/groups', GroupShoesController::class);
Route::apiResource('/shoes/models', ModelsShoesController::class);
// Допоміжний маршрут для завантаження списків груп та моделей у формах
Route::get('warehouse-shoes/form-data', [WarehouseShoesController::class, 'formData']);
// Основні REST API маршрути
Route::apiResource('warehouse-shoes', WarehouseShoesController::class);