<?php

namespace App\Http\Controllers;

use App\Models\ModelsShoes;
use App\Http\Requests\StoreModelsShoesRequest;
use App\Http\Requests\UpdateModelsShoesRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ShoesModelsResource;

class ModelsShoesController extends Controller
{
    /**
     * Отримання списку груп з пагінацією.
     */
    public function index(): JsonResponse
    {
        // Замініть GroupShoes на вашу модель, якщо вона називається інакше
        $groups = ModelsShoes::latest('id')->paginate(10);

        return response()->json($groups);
    }

    public function getAll()
    {
        // Замініть GroupShoes на вашу модель, якщо вона називається інакше
        //

        $groups = ModelsShoes::whereActive(true)->orderBy('name', 'asc')->get();

        return ShoesModelsResource::collection($groups);
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

        $group = ModelsShoes::create($validated);

        return response()->json([
            'message' => 'Групу успішно створено',
            'data' => $group,
        ], 201);
    }

    /**
     * Перегляд однієї групи.
     */
    public function show(ModelsShoes $groupShoes): JsonResponse
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
         $groupShoes = ModelsShoes::findOrFail( (int)$groupShoesId);

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
        $groupShoes = ModelsShoes::findOrFail( (int)$groupShoesId);
        $groupShoes->delete();

        return response()->json([
            'message' => 'Модель успішно видалено',
        ]);
    }

    public function showSPA()
    {
        return view('biz.models');
    }
}
