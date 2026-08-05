<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\ModelsShoes;
use App\Models\WarehouseShoes;

class ShoesModels implements ToModel
{

public function __construct(public $groupId) {}
    
     public function model(array $row)
    {
        // dd($row);
        $sizes = trim($row[1]);
        $sizes = str_replace(".", ",", $sizes);
        $sizes = preg_replace("#[^\d\,]#si", "", $sizes);
        $js = explode(",", $sizes);
        $js = array_map(function ($item) {
            if (empty($item)) return null;
            $item = trim($item);
            $item = intval($item);
            if ($item > 10) return $item;
            return ($item > 5 && $item <= 9) ? $item+30 : $item+40 ;
        }, $js);
       $js = collect($js)
        ->whereNotNull()
        ->values();
        $row[0] = (string)$row[0];
        if (empty($row[0])) return ;
        $m = ModelsShoes::updateOrCreate([
            'name' => $row[0],
        ], [
            'name' => $row[0],
        ]);

       return WarehouseShoes::updateOrCreate([
            'models_id' => $m->id,
            'group_id' => $this->groupId,
        ], [
            'models_id' => $m->id,
            'group_id' => $this->groupId,
            'sizes' => $js->toArray(),
            'residual' => $js->count(),
        ]);
    }
}
