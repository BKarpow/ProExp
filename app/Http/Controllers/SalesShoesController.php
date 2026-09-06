<?php
namespace App\Http\Controllers;

use App\Models\SalesShoes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalesShoesController extends Controller
{
    /**
     * Отримати список усіх продажів з пагінацією та зв'язками.
     */
    public function index(Request $request): JsonResponse
    {
        $sales = SalesShoes::with(['model', 'user'])
            ->latest()
            ->paginate(15);

        return response()->json($sales);
    }

    /**
     * Зберегти новий запис про продаж.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'models_id' => 'required|exists:models_shoes,id',
            'size'      => 'required|integer|max:60|min:20',
            'price'     => 'required|integer|min:0',
            'active'    => 'boolean',
            // user_id можна передати з фронтенду або підтягнути з авторизації
            'user_id'   => 'nullable|exists:users,id',
        ]);

        // Якщо user_id не передано явно в тілі запиту, беремо ID авторизованого користувача
        $validated['user_id'] = $validated['user_id'] ?? $request->user()?->id;

        $sale = SalesShoes::create($validated);
        $sale->load(['model', 'user']);

        return response()->json([
            'message' => 'Запис успішно створено',
            'data'    => $sale,
        ], 201);
    }

    /**
     * Відобразити конкретний запис.
     */
    public function show(SalesShoes $salesShoe): JsonResponse
    {
        $salesShoe->load(['model', 'user']);

        return response()->json($salesShoe);
    }

    /**
     * Оновити існуючий запис про продаж.
     */
    public function update(Request $request, SalesShoes $salesShoe): JsonResponse
    {
        $validated = $request->validate([
            'models_id' => 'sometimes|required|exists:models_shoes,id',
            'size'      => 'sometimes|required|string|max:255',
            'price'     => 'sometimes|required|integer|min:0',
            'active'    => 'boolean',
            'user_id'   => 'nullable|exists:users,id',
        ]);

        $salesShoe->update($validated);
        $salesShoe->load(['model', 'user']);

        return response()->json([
            'message' => 'Запис успішно оновлено',
            'data'    => $salesShoe,
        ]);
    }

    /**
     * Видалити запис про продаж.
     */
    public function destroy(SalesShoes $salesShoe): JsonResponse
    {
        $salesShoe->delete();

        return response()->json([
            'message' => 'Запис успішно видалено',
        ]);
    }

    public function showSPA() {
        return view('biz.sales');
    }
}
