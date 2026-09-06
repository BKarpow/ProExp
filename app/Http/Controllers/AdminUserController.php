<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\CacheHandleTrait;


class AdminUserController extends Controller
{

    use CacheHandleTrait;

    public function index(Request $request)
    {
        return view('admin.user.index', [
            'data' => User::paginate(50)
        ]);
    }

    public function clearCache(Request $request)
    {
        $request->validate([
            'user' => 'required|numeric'
        ]);
        $this->clearAllCacheUser($request->user);
        return redirect()->route('admin.user.index')->withStatus("Кеш очищено");
    }

    public function changeRoleUserSetManager(Request $request) {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);
        $u = User::findOrFail($request->user_id);
        $u->role = User::ROLE_MANAGER;
        $u->save();
        return redirect()
        ->back()
        ->withStatus('Права менеджера встановлено для користувача '.$u->name);
    }
}
