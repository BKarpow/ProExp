<?php

namespace App\Services;

use App\Models\SalesShoes;

trait ShoeseSaveService {


    private function saveSalePrice(int $models_id, int $size, int $price, int $user_id): bool
    {
        $s = new SalesShoes();
        $s->models_id = $models_id;
        $s->user_id = $user_id;
        $s->size = $size;
        $s->price = $price;
        return (bool)$s->save();
    }
}
