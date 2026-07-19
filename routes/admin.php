<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/test', function() {
        dd(\Illuminate\Support\Facades\Auth::user()->expiredDays());
    })->name('admin.test');
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/run-migrate', [AdminController::class, 'runMigrate'])->name('admin.runMigrate');
    Route::get('/user/', [AdminUserController::class, 'index'])->name('admin.user.index');
    Route::post('/user/clear/cache', [AdminUserController::class, 'clearCache'])->name('admin.user.clearCache');

    Route::get('/mail/test', [AdminController::class, 'sendTestMail'])->name('admin.mail.test');
    Route::get('/mail/telegram/test', [AdminController::class, 'sendTestTelegramExps'])
    ->name('admin.mail.telegram.test');

    Route::get('/cache/clear', [AdminController::class, 'clearConfigCache'])->name('admin.cache.clear');
    Route::get('/optios', [AdminController::class, 'optionsPage'])->name('admin.options');

    Route::get('/test-mail', function () {
    Artisan::call('config:clear');
    try {
        Mail::raw('Тестовий лист без перевірки SSL', function ($message) {
            $message->to('karpov.bohdan@productexp.pp.ua')->subject('Перевірка');
        });
        return "Лист успішно відправлено!";
    } catch (\Exception $e) {
        return "Помилка: " . $e->getMessage();
    }
});
});
