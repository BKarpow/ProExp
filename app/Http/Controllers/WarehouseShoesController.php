<?php

namespace App\Http\Controllers;

use App\Models\WarehouseShoes;
use App\Models\GroupShoes;
use App\Models\ModelsShoes;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WarehouseShoesController extends Controller
{
    /**
     * Отримання списку залишків на складі з пагінацією та зв'язками.
     */
    public function index(): JsonResponse
    {
        $items = WarehouseShoes::with(['group', 'model'])
        ->where('active', true)
            ->latest('id')
            ->paginate(200);

        return response()->json($items);
    }

    /**
     * Отримання допоміжних даних для випадаючих списків (Групи та Моделі).
     */
    public function formData(): JsonResponse
    {
        return response()->json([
            'groups' => GroupShoes::select('id', 'name')->where('active', true)->get(),
            'models' => ModelsShoes::select('id', 'name')->where('active', true)->get(),
        ]);
    }

    /**
     * Збереження нового запису.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:group_shoes,id',
            'models_id' => 'required|exists:models_shoes,id',
            'sizes' => 'nullable|array',
            'residual' => 'nullable|integer|min:0',
            'price' => 'nullable|integer|min:0',
            'active' => 'boolean',
        ]);

        $item = WarehouseShoes::create($validated);

        return response()->json([
            'message' => 'Запис успішно додано',
            'data' => $item->load(['group', 'model']),
        ], 201);
    }

    /**
     * Оновлення запису.
     */
    public function update(Request $request, WarehouseShoes $warehouseShoe): JsonResponse
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:group_shoes,id',
            'models_id' => 'required|exists:models_shoes,id',
            'sizes' => 'nullable|array',
            'residual' => 'nullable|integer|min:0',
            'price' => 'nullable|integer|min:0',
            'active' => 'boolean',
        ]);

        $warehouseShoe->update($validated);

        return response()->json([
            'message' => 'Запис успішно оновлено',
            'data' => $warehouseShoe->load(['group', 'model']),
        ]);
    }

    /**
     * Видалення запису.
     */
    public function destroy(WarehouseShoes $warehouseShoe): JsonResponse
    {
        $warehouseShoe->delete();

        return response()->json([
            'message' => 'Запис успішно видалено',
        ]);
    }

    public function showSPA()
    {
        return view('biz.warehouse');
    }
}