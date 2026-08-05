<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\DateProduct;
use Illuminate\Support\Facades\Auth;
use App\Imports\DateProductImport;
use App\Imports\ImportProductList;
use App\Imports\ShoesModels;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function import(Request $request)
    {
        return view('import');
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);
        // Отримуємо файл
        $image = $request->file('file');

        // Генеруємо унікальне ім'я файлу
        $fileName = 'import_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

        // Зберігаємо оригінальне зображення
        $imagePath = $image->storeAs('imports', $fileName, 'public');
        $data = $this->parseCsv($imagePath);
        dd($data);
    }

    private function parseCsv(string $fp): array
    {
        if (!is_file($fp)) return [];
        $data = [];
        if (($handle = fopen($fp, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            }
            fclose($handle);
        }
        return $data;
    }

    private function reFornatDate(string $d): string
    {
        return date('Y.m.d', strtotime($d . "00:00:00"));
    }

    public function upload(Request $request)
    {
        // Валідація
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $path = $request->file('file')->getRealPath();
        $file = fopen($path, 'r');

        // Читаємо заголовки

        $head = fgetcsv($file);
        $data = [];
        while (($data[] = fgetcsv($file)) !== false) {
            // $data = array_combine($headers, $row);

        }
        fclose($file);
        foreach ($data as $d) {
            if (!empty($d[5])) {
                $p = Product::whereBarcode($d[5])->first();
                if (!$p) {
                    $p = new Product();
                    $p->name = $d[0];
                    $p->barcode = $d[5];
                    $p->description = $d[3];
                    $p->save();
                }
                // $dp = new DateProduct();
                // $dp->group_id = Auth::user()->configDefaultGroup();
                // $dp->product_id = $p->id;
                // $dp->user_id = Auth::id();
                // $dp->start = $this->reFornatDate($d[1]);
                // $dp->end = $this->reFornatDate($d[2]);
                // $dp->count = (int)$d[3];
                // $dp->save();
                DateProduct::updateOrCreate(
                    ["product_id" => $p->id, "user_id" => Auth::id(), "start" => $this->reFornatDate($d[1]), "end" => $this->reFornatDate($d[2])],
                    [
                        "group_id" => Auth::user()->configDefaultGroup(),
                        "product_id" => $p->id,
                        "user_id" => Auth::id(),
                        "start" => $this->reFornatDate($d[1]),
                        "end" => $this->reFornatDate($d[2]),
                        "count" => (int)$d[3],
                        "created_at" => now(),
                        "updated_at" => now()
                    ]
                );
            }
        }
        return back()->with('status', 'Файл успішно оброблено!');
    }

    public function uploadExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        // Один рядок для всього імпорту
        Excel::import(new DateProductImport, $request->file('file'));

        return back()->with('status', 'Excel файл успішно імпортовано!');
    }


    public function uploadProductExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        // Один рядок для всього імпорту
        Excel::import(new ImportProductList, $request->file('file'));

        return back()->with('status', 'CSV Список продуктів успішно імпортовано!');
    }

    public function uploadModelsShoesExcel(Request $request)
    {
        // dd($request);
         $d = $request->validate([
            'file1' => 'required|mimes:xlsx,xls,csv|max:2048',
            'groupId' => 'required|integer|exists:group_shoes,id'
        ]);
        

        // Один рядок для всього імпорту
        Excel::import(new ShoesModels($d["groupId"]), $request->file('file1'));

        return back()->with('status', 'CSV Список продуктів успішно імпортовано!');
    }
}
