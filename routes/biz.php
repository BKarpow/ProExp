<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupShoesController;
use App\Http\Controllers\ModelsShoesController;
use App\Http\Controllers\WarehouseShoesController;
use App\Http\Controllers\SalesShoesController;
use Illuminate\Support\Facades\Artisan;

Route::get('/shoes/models/all', [ModelsShoesController::class,'getAll'])
->name('api.shoes.get.models');

Route::middleware(['auth'])->prefix('shoes/group')->group(function () {
    Route::get('/', [GroupShoesController::class, 'showSPA'])
        ->name('shoes.group');
        Route::get('/sales', [SalesShoesController::class, 'showSPA'])
            ->name('shoes.sales');



});







Route::middleware(['auth'])->prefix('shoes/models')->group(function () {
    Route::get('/', [ModelsShoesController::class, 'showSPA'])
        ->name('shoes.models');



});


Route::middleware(['auth'])->prefix('shoes/warehouse')->group(function () {
    Route::get('/', [WarehouseShoesController::class, 'showSPA'])
        ->name('shoes.warehouse');



});

Route::middleware(['auth', 'admin'])->prefix('shoes/warehouse/admin')->group(function () {
    Route::get('/clear', [WarehouseShoesController::class, 'clearTable'])
        ->name('shoes.warehouse.clear');



});
