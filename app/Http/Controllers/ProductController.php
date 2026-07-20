<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Rules\Ean13;
use App\Models\ImageProduct;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller    
{
    private function getToApi(int $code)
    {
        $res = Product::where('barcode', $code)->first();
        if ($res !== null) {
            return [
                'status_code' => 200,
                'isDB' => true,
                'json_response' => ['name' => $res->productName(), 'id' => $res->id, 'comment' => $res->comment], // якщо очікується JSON
            ];
        }
        try {
            $url = "https://stores-api.zakaz.ua/uber_catalog/products/search/";

            $response = Http::withHeaders([
                'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0',
                'Accept'          => '*/*',
                'Accept-Language' => 'uk',
                'Referer'         => 'https://zakaz.ua/',
                'Content-Type'    => 'application/json',
                'x-chain'         => '*',
                'x-version'       => '67',
                'Origin'          => 'https://zakaz.ua',
                'Connection'      => 'keep-alive',
                'Sec-Fetch-Dest'  => 'empty',
                'Sec-Fetch-Mode'  => 'cors',
                'Sec-Fetch-Site'  => 'same-site',
                'Priority'        => 'u=4',
                'TE'              => 'trailers',
            ])->timeout(7)->get($url, [
                'q'         => $code,
                'per_page'  => 10,
                'sort'      => 'relevance_desc'
            ]);

            // Перевіряємо, чи сервер взагалі відповів успішно (код 200)
            if ($response->failed()) {
                return [
                    'status_code' => 502,
                    'response' => "",
                    'isDB' => false,
                    'json_response' => ['name' => ''], // якщо очікується JSON
                ];
            }
            $data = $response->json();
            if (!empty($data['results']) && isset($data['results'][0])) {
                $product = $data['results'][0];
                $imageUrl = $product['img']['s350x350'] ?? $product['img']['s150x150'] ?? null;
                $productName = $product['title'] ?? 'Назва відсутня';
                $this->saveNewProduct($code, $productName);
                $this->saveProductImage($imageUrl, $code);
                return [
                    'status_code' => $response->status(),
                    'response' => $response->body(),
                    'isDB' => false,
                    'json_response' => ['name' => $productName], // якщо очікується JSON
                ];
            } else {
                return [
                    'status_code' => 205,
                    'response' => "",
                    'isDB' => false,
                    'json_response' => ['name' => ''], // якщо очікується JSON
                ];
            }
        } catch (\Exception $e) {
            // Обробка форс-мажорів (наприклад, відсутній інтернет на сервері)
            return response()->json([
                'error' => 'Виникла помилка під час обробки запиту',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    private function saveNewProduct(string $code, string $name):void {
        $p = new Product();
                $p->barcode = $code;
                $p->name = $productName;
                $p->save();
    }

    private function saveProductImage(?string $imageUrl, string $barcode): ?string
    {
        // Якщо картинки взагалі немає у відповіді API
        if (empty($imageUrl)) {
            return null;
        }

        try {
            // 1. Завантажуємо бінарні дані картинки
            $imageResponse = Http::timeout(5)->get($imageUrl);

            if ($imageResponse->failed()) {
                return null;
            }

            // 2. Визначаємо розширення файлу (jpg, png тощо) з URL або ставимо jpg за замовчуванням
            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';

            // Формуємо безпечне ім'я файлу, наприклад: 4820046964557_a1b2c3.jpg
            $fileName = 'product_' . $barcode . '.' . $extension;
            $directory = 'products'; // Папка всередині storage/app/public/
            $fullPath = $directory . '/' . $fileName;

            // 3. Зберігаємо файл у диск 'public' (це папка storage/app/public)
            Storage::disk('public')->put($fullPath, $imageResponse->body());

            // Повертаємо шлях для збереження в базу даних (наприклад: products/4820046964557_a1b2c3.jpg)
            return $fullPath;
        } catch (\Exception $e) {
            // Якщо щось пішло не так (наприклад, таймаут або немає інтернету), просто ігноруємо і повертаємо null
            logger()->error("Не вдалося завантажити картинку для штрих-коду {$barcode}: " . $e->getMessage());
            return null;
        }
    }

    public function getProductName(Request $request)
    {
        $d = $request->validate([
            'barcode' => ['required', new Ean13]
        ]);
        return response()->json($this->getToApi((int) $d['barcode']));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('product.index', [
            'data' => Product::orderBy('name', 'asc')->paginate(50),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('product.create');
    }

    private function stringToBoolConv(string $val): bool
    {
        if ($val === "true") return true;
        elseif ($val === "false") return false;
        else return false;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        // dd($request->all());
        if ($this->stringToBoolConv($request->isExistsProduct)) {
            $p = Product::findOrFail((int)$request->idExistsProduct);
            if (!$p) abort(404);
        } else {
            $p = new Product();
            $p->barcode = $request->barcode;
        }
        $p->name = $request->name;
        $p->description = $request->comment ?? '';
        $p->save();
        if ($this->stringToBoolConv($request->isExistsImage)) {
            $request->validate([
                'imageName' => 'required|string|max:250|min:7',
            ]);
            $i = new ImageProduct();
            $i->product_id = $p->id;
            $i->path = $request->imageName;
            $i->save();
        }
        return redirect()->route('product.index')->withStatus("Product created!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('product.edit', ['item' => $product]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->name = $request->name;
        $product->save();
        return redirect()->route('product.index')->withStatus("Продукт оновлено!{$product->id}: {$product->name}");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
