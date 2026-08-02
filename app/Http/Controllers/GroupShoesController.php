<?php

namespace App\Http\Controllers;

use App\Models\GroupShoes; // Або ваша модель, наприклад Category
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GroupShoesController extends Controller
{
    /**
     * Отримання списку груп з пагінацією.
     */
    public function index(): JsonResponse
    {
        // Замініть GroupShoes на вашу модель, якщо вона називається інакше
        $groups = GroupShoes::latest('id')->paginate(10);

        return response()->json($groups);
    }

    /**
     * Створення нової групи.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string|max:255',
        ]);

        $group = GroupShoes::create($validated);

        return response()->json([
            'message' => 'Групу успішно створено',
            'data' => $group,
        ], 201);
    }

    /**
     * Перегляд однієї групи.
     */
    public function show(GroupShoes $groupShoes): JsonResponse
    {
        return response()->json($groupShoes);
    }

    /**
     * Оновлення групи.
     */
    public function update(Request $request, $groupShoesId): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string|max:255',
        ]);
         $groupShoes = GroupShoes::findOrFail( (int)$groupShoesId);
        
        $groupShoes->update($validated);

        return response()->json([
            'message' => 'Групу успішно оновлено',
            'data' => $groupShoes,
        ]);
    }

    /**
     * Видалення групи.
     */
    public function destroy($groupShoesId): JsonResponse
    {   
        $groupShoes = GroupShoes::findOrFail( (int)$groupShoesId);
        $groupShoes->delete();

        return response()->json([
            'message' => 'Групу успішно видалено',
        ]);
    }

    public function showSPA()
    {
        return view('biz.group');
    }
}