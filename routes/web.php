<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CronController;
use App\Http\Controllers\TelegramHandlerController;
use App\Http\Middleware\EnsurePhoneIsSet;
use App\Http\Controllers\AutoImageProductController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\InfoApiController;


// Route::get('/test-ssl', function () {
//     try {
//         $response = Http::get('https://google.com');
//         return "Статус запиту: " . $response->status();
//     } catch (\Exception $e) {
//         return "Помилка SSL: " . $e->getMessage();
//     }
// });


Route::get('/testi', [AutoImageProductController::class, 'runAuto']);

use App\Http\Controllers\ProfileCompletionController;

Route::middleware(['auth'])->group(function () {
    Route::get('/complete', [ProfileCompletionController::class, 'edit'])
    ->withoutMiddleware([EnsurePhoneIsSet::class])
    ->name('profile.complete');
    Route::post('/complete-profile', [ProfileCompletionController::class, 'update'])
    ->withoutMiddleware([EnsurePhoneIsSet::class])
    ->name('profile.complete.update');
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tools', function () {
    return view('tools');
})->name('tools');

//  Route::get('/run-migrations', function () {
//      try {
//          // Виконуємо команду 'migrate'
//          Artisan::call('migrate', ['--force' => true]);

//          // Отримуємо результат виконання команди
//          $output = Artisan::output();

//          return response()->json([
//              'status' => 'success',
//              'message' => 'Міграції виконано успішно.',
//              'output' => $output
//          ]);

//      } catch (\Exception $e) {
//          return response()->json([
//              'status' => 'error',
//              'message' => 'Помилка під час виконання міграцій: ' . $e->getMessage()
//          ], 500);
//      }
//  })->middleware('web');

Auth::routes();

Route::get('/telegram-bind', [TelegramHandlerController::class, 'bind'])->name('telegram.bind');

Route::get('/', [App\Http\Controllers\DateProductController::class, 'index'])->name('index');
Route::get('/home', [App\Http\Controllers\DateProductController::class, 'index'])->name('home');
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/shop/add', [App\Http\Controllers\ShopController::class, 'create'])
->name('shop.create');

Route::post('/shop/add', [App\Http\Controllers\ShopController::class, 'store'])
->name('shop.create.store');

Route::get('/cron/not', [CronController::class, 'run']);

require __DIR__.'/shop.php';
require __DIR__.'/groups.php';
require __DIR__.'/product.php';
require __DIR__.'/exp.php';
require __DIR__.'/config.php';
require __DIR__.'/import.php';
require __DIR__.'/telegram.php';




$prefixRoute = "/api";

Route::get($prefixRoute.'/shops', [InfoApiController::class, 'getShops'])
->name('api.info.shops');

// getGroupsFromShop

Route::get($prefixRoute.'/groups', [InfoApiController::class, 'getGroupsFromShop'])
->name('api.info.shops');

$prefixRoute = null;



require __DIR__.'/admin.php';
require __DIR__.'/wproduct.php';
require __DIR__.'/inventory.php';
require __DIR__.'/biz.php';
