<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\TelegramHandler;

class CheckTelegramBinding
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Перевіряємо, чи користувач авторизований
        // та чи ПУСТЕ поле telegram_id

        if ($request->user() && !$request->routeIs('telegram.bind')) {
            if (!TelegramHandler::where('user_id', $request->user()->id)->exists() &&
            !$request->user()->isManager()) {

                return redirect()->route('telegram.bind')
                    ->with('warning', 'Будь ласка, прив’яжіть Telegram для продовження.');
            }
        } elseif ($request->routeIs('telegram.bind') && TelegramHandler::where('user_id', $request->user()->id)->exists()) {
                return redirect()->route('home');
        }

        return $next($request);
    }
}
