<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupShoesController;
use App\Http\Controllers\ModelsShoesController;
use App\Http\Controllers\WarehouseShoesController;
use Illuminate\Support\Facades\Artisan;

Route::middleware(['auth'])->prefix('shoes/group')->group(function () {
    Route::get('/', [GroupShoesController::class, 'showSPA'])
        ->name('shoes.group');

        
    
});







Route::middleware(['auth'])->prefix('shoes/models')->group(function () {
    Route::get('/', [ModelsShoesController::class, 'showSPA'])
        ->name('shoes.models');

        
    
});


Route::middleware(['auth'])->prefix('shoes/warehouse')->group(function () {
    Route::get('/', [WarehouseShoesController::class, 'showSPA'])
        ->name('shoes.warehouse');

        
    
});
