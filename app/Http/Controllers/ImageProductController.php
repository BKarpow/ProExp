<?php

namespace App\Http\Controllers;

use App\Models\ImageProduct;
use App\Models\Product;
use App\Http\Requests\StoreImageProductRequest;
use App\Http\Requests\UpdateImageProductRequest;
use App\Rules\Ean13;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ImageProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreImageProductRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ImageProduct $imageProduct)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ImageProduct $imageProduct)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateImageProductRequest $request, ImageProduct $imageProduct)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ImageProduct $imageProduct)
    {
        //
    }


    public function isExistsImage(Request $request)
    {
        $request->validate(
            [
                'barcode' => ['required', new Ean13]
            ]
        );
        $product = Product::whereBarcode($request->barcode)->first();
        return response()->json([
            'existsImage' => (bool)$product,
            'urlImage' => (!$product) ? asset('storage/products/no-image.png') : $product->mainImg()
        ]);
    }


    public function storeApi(Request $request)
    {
        // Валідація
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
            'product_id' => 'required|numeric'
        ]);

        try {


            // Отримуємо файл
            $image = $request->file('image');

            // Генеруємо унікальне ім'я файлу
            $fileName = 'product_' . $request->product_id . '.' . $image->getClientOriginalExtension();

            // Зберігаємо оригінальне зображення
            $imagePath = $image->storeAs('products', $fileName, 'public');

            // Опціонально: створюємо thumbnail

            $path = $image->storeAs('products', $fileName, 'public');
            // Повертаємо відповідь
            return response()->json([
                'success' => true,
                'id' => $request->product_id,
                'url' => '/storage/' . $path,
                'file_name' => $fileName,
                'message' => 'Фото успішно завантажено'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Помилка завантаження фото: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Розпізнати термін придатності на зображенні за допомогою Gemini 2.5 Flash.
     */
    public function detectExpiryDate(Request $request): JsonResponse
    {
        // 1. Валідація: перевіряємо, чи завантажено файл і чи це картинка
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:4096', // ліміт 4МБ
        ]);

        // 2. Беремо файл та конвертуємо його в Base64
        $imagePath = $request->file('image')->path();
        $image = $request->file('image');
        $path = $request->file('image')->store('screenDates', 'public');
        $mimeType = $request->file('image')->getMimeType();
        $base64Image = base64_encode(file_get_contents($imagePath));
        $pid = $request->input('pid');
        if ($pid && $p = Product::find($pid)) {
            $i = new ImageProduct();
            $i->product_id = $p->id;
            $i->path = $path;
            $i->save();
        }

        // 3. Отримуємо API ключ із конфігу
        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            return response()->json(['error' => 'Gemini API Key не налаштовано в .env'], 500);
        }

        // Використовуємо швидку та безкоштовну модель gemini-2.5-flash
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        // Промт (інструкція для ШІ)
        $prompt = "Уважно подивись на це фото пакування продукту. Знайди термін придатності " .
            "(це може бути кінцева дата, або дата виробництва + строк зберігання). " .
            "Поверни відповідь СУВОРО у форматі JSON: {\"expiry_date\": \"DD.MM.YY\"}. " .
            "Якщо дату розпізнати неможливо або її немає, поверни: {\"expiry_date\": null}. " .
            "Не пиши нічого, крім JSON. Без зайвих слів, маркдауну та оформлення ```json.";

        try {
            // 4. Робимо POST-запит за структурою Google API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(15)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inlineData' => [
                                    'mimeType' => $mimeType,
                                    'data'     => $base64Image
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Помилка запиту до Gemini API',
                    'details' => $response->body()
                ], $response->status());
            }

            // 5. Дістаємо текст відповіді ШІ
            $aiResponseText = $response->json('candidates.0.content.parts.0.text');

            // Оскільки ми просили суворий JSON, очищаємо пробіли та декодуємо його
            $cleanedJson = trim($aiResponseText);
            $resultData = json_decode($cleanedJson, true);

            // Якщо ШІ повернув не валідний JSON (буває рідко), повертаємо як текст
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'raw_response' => $aiResponseText,
                    'message' => 'ШІ повернув дані у неформатованому вигляді'
                ]);
            }


            return response()->json([
                'success' => true,
                'data' => $resultData,
                'pathScreen' => $path,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Внутрішня помилка при розпізнаванні зображення',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Розпізнати цінник на зображенні за допомогою Gemini 2.5 Flash.
     */
    public function detectNameProductAndBsrcode(Request $request): JsonResponse
    {
        // 1. Валідація: перевіряємо, чи завантажено файл і чи це картинка
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:4096', // ліміт 4МБ
        ]);

        // 2. Беремо файл та конвертуємо його в Base64
        $imagePath = $request->file('image')->path();
        $image = $request->file('image');
        $path = $request->file('image')->store('pricesMark', 'public');
        $mimeType = $request->file('image')->getMimeType();
        $base64Image = base64_encode(file_get_contents($imagePath));

        // 3. Отримуємо API ключ із конфігу
        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            return response()->json(['error' => 'Gemini API Key не налаштовано в .env'], 500);
        }

        // Використовуємо швидку та безкоштовну модель gemini-2.5-flash
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        // Промт (інструкція для ШІ)
        $prompt = "Уважно подивись на це фото цінника. Знайди назву товару та штрихкод " .

            "Використовуй прапорець success як індекатор того що ти все зміг розібрати на фото " .
            "Поверни відповідь СУВОРО у форматі JSON: {\"success\": \"bool\", \"name\": \"Product name\", \"barcode\": ...}. " .
            "Якщо назву продукту або штрих код розпізнати неможливо або її немає, поверни: {\"name\": null, \"barcode\": null}. " .
            "Не пиши нічого, крім JSON. Без зайвих слів, маркдауну та оформлення ```json.";

        try {
            // 4. Робимо POST-запит за структурою Google API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(15)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inlineData' => [
                                    'mimeType' => $mimeType,
                                    'data'     => $base64Image
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Помилка запиту до Gemini API',
                    'details' => $response->body()
                ], $response->status());
            }

            // 5. Дістаємо текст відповіді ШІ
            $aiResponseText = $response->json('candidates.0.content.parts.0.text');

            // Оскільки ми просили суворий JSON, очищаємо пробіли та декодуємо його
            $cleanedJson = trim($aiResponseText);
            $resultData = json_decode($cleanedJson, true);

            // Якщо ШІ повернув не валідний JSON (буває рідко), повертаємо як текст
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'raw_response' => $aiResponseText,
                    'message' => 'ШІ повернув дані у неформатованому вигляді'
                ]);
            }
            $p = null;
            if ($resultData['success']) {
                Product::updateOrCreate([
                    'barcode' => $resultData['barcode']
                ], [
                    'name' => $resultData['name'],
                    'barcode' => $resultData['barcode']
                ]);
            }


            return response()->json([
                'success' => $resultData['success'],
                // 'createdrProduct' => (bool)$p,
                'data' => $resultData,
                'pathScreen' => $path,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Внутрішня помилка при розпізнаванні зображення',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
