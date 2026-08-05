<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\DateProduct;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewDateProductMail;
use App\Models\GroupShoes;


class AdminController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.index', [
            'products' => Product::count(),
            'exps' => DateProduct::count(),
            'users' => User::count(),
            'lastDate' => DateProduct::orderBy('created_at', 'desc')->first(),
            'lastProduct' => Product::select('name', 'created_at', 'updated_at')->orderBy('created_at', 'desc')->first()
        ]);
    }

    public function runMigrate()
    {
        try {
         // Виконуємо команду 'migrate'
         Artisan::call('migrate', ['--force' => true]);

         // Отримуємо результат виконання команди
         $output = Artisan::output();

         return redirect()->back()->withStatus('Міграції виконано успішно.'."<br><pre>{$$output}</pre>");

     } catch (\Exception $e) {
        return redirect()->back()->withStatus('Помилка під час виконання міграцій: ' . $e->getMessage());

     }
    }

    public function clearConfigCache() {
        //php artisan config:clear
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        return redirect()->back()->withStatus("Кеші (config, cache, view, route) очищено");
    }

    public function sendTestMail()
    {
        Mail::to(env('MAIL_ADMIN'))->send(new NewDateProductMail("Перевірка 1"));
        return redirect()->back()->withStatus("Лист в черзі");
    }

    public function optionsPage()
    {

        return view('admin.options', [
            'groups' => GroupShoes::select('id', 'name')->orderBy('name', 'asc')->get()
        ]);
    }

    public function sendTestTelegramExps(Request $request)
    {
        event(new \App\Events\SendExpire($request->user()));
        return redirect()->back()->withStatus("Відправлено повідомлення в telegram.");
    }
}
