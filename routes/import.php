<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;

$prefixRoute = "/import";
$nameAlias = 'import.';

Route::get($prefixRoute.'/csv', [ImportController::class, 'import'])
->name('import.csv');
Route::post($prefixRoute.'/csv', [ImportController::class, 'uploadExcel'])
->name('import.csv');

Route::post($prefixRoute.'/csv', [ImportController::class, 'uploadProductExcel'])
->name('import.product.csv');


//uploadModelsShoesExcel

Route::post($prefixRoute.'/shoes/models/csv', [ImportController::class, 'uploadModelsShoesExcel'])
->name('import.models.csv');


$prefixRoute = null;
$nameAlias = null;

