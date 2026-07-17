<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ImageProductController;

$prefixRoute = "/product";

Route::get($prefixRoute.'/', [ProductController::class, 'index'])
->name('product.index');

Route::get($prefixRoute.'/new', [ProductController::class, 'create'])
->name('product.create');

Route::get($prefixRoute.'/edit/{product}', [ProductController::class, 'edit'])
->name('product.edit');

Route::post($prefixRoute.'/edit/{product}', [ProductController::class, 'update'])
->name('product.edit');

Route::post($prefixRoute.'/upload/image', [ImageProductController::class, 'storeApi'])
->name('product.upload.image');

Route::post($prefixRoute. '/image/exists', [ImageProductController::class, 'isExistsImage'])
->name('product.exists.image');

Route::post($prefixRoute.'/new', [ProductController::class, 'store'])
->name('product.create');

Route::post($prefixRoute.'/getname', [ProductController::class, 'getProductName'])
->name('product.getname');

Route::post($prefixRoute.'/detect/exp', [ImageProductController::class, 'detectExpiryDate'])
->middleware('throttle:5,1')->name('detect.exp');

Route::post($prefixRoute.'/detect/prices', [ImageProductController::class, 'detectNameProductAndBsrcode'])
->middleware('throttle:5,1')->name('detect.prices');

$prefixRoute = null;

