<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DateProductController;
use App\Http\Controllers\Api\ExpiryController;

$prefixRoute = "/date";
$nameAlias = 'date.';

Route::get($prefixRoute.'/', [DateProductController::class, 'index'])
->name('date.index');

Route::get($prefixRoute.'/show/{dateProduct}', [DateProductController::class, 'show'])
->name('date.show');

Route::get($prefixRoute.'/group/{group}', [DateProductController::class, 'indexGroup'])
->name('date.group');

Route::get($prefixRoute.'/expired', [DateProductController::class, 'expired'])
->name('date.expired');

Route::get($prefixRoute.'/expired/{group}', [DateProductController::class, 'expiredGroup'])
->name('date.expiredGroup');

Route::get($prefixRoute.'/create', [DateProductController::class, 'create'])
->name('date.create');

Route::post($prefixRoute.'/create', [DateProductController::class, 'store'])
->name('date.create');

Route::get($prefixRoute.'/edit/{dateProduct}', [DateProductController::class, 'edit'])
->name('date.edit');

Route::post($prefixRoute.'/edit/{dateProduct}', [DateProductController::class, 'update'])
->name('date.edit');

// dateExists

Route::post($prefixRoute.'/info/exists', [DateProductController::class, 'dateExists'])
->name('date.info.exists');

Route::delete($prefixRoute.'/del/{dateProduct}', [DateProductController::class, 'destroy'])
->name('date.delete');

Route::get($prefixRoute.'/search/', [DateProductController::class, 'search'])
->name('date.search');

// searchForBarcode
Route::post($prefixRoute.'/search/barcode', [DateProductController::class, 'searchForBarcode'])
->name('date.search.barcode');


Route::get($prefixRoute.'/delete-image/{dateProduct}', [DateProductController::class, 'delImg'])
->name('date.delImg');



Route::get($prefixRoute.'/show/user/{user}', [DateProductController::class, 'userExps'])
->name('date.userExps');

Route::post($prefixRoute.'/api/analyze-expiry', [ExpiryController::class, 'analyze'])
->name('date.analyze');

//showToDo

Route::get($prefixRoute.'/todo', [DateProductController::class, 'showToDo'])
->name('date.todo');

// setDone

Route::get($prefixRoute.'/api/set/done/{dateProduct}', [DateProductController::class, 'setDone'])
->name('date.set.done');

// getToDoDateList

Route::get($prefixRoute.'/api/get/todo/', [DateProductController::class, 'getToDoDateList'])
->name('date.get.todo');

// setMarkdown

Route::get($prefixRoute.'/api/set/mark/{dateProduct}', [DateProductController::class, 'setMarkdown'])
->name('date.set.markdown');

//getMarkdownDateList

Route::get($prefixRoute.'/api/get/mark/', [DateProductController::class, 'getMarkdownDateList'])
->name('date.get.markdown');

// getListExpiredProducts

Route::get($prefixRoute.'/api/get/expired', [DateProductController::class, 'getListExpiredProducts'])
    ->name('date.get.expired');

    //getListExpiredProductsEnd4Days

Route::get($prefixRoute.'/api/get/expired/filter', [DateProductController::class, 'getListExpiredProductsEndFilter'])
    ->name('date.delete.from.group');

//deleteAllDateFromGroup

Route::delete($prefixRoute.'/api/delete/from/group/{product}', [DateProductController::class, 'deleteAllDateFromGroup'])
    ->name('date.delete.from.group');

$prefixRoute = null;
$nameAlias = null;

