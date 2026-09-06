<?php

namespace App\Observers;

use App\Models\WarehouseShoes;
use App\Models\SalesShoes;

class WarehouseOrserver
{
    /**
     * Handle the WarehouseShoes "created" event.
     */
    public function saved(WarehouseShoes $warehouseShoes): void
    {
        $s = new SalesShoes();
        $s->models_id = $warehouseShoes->models_id;
        $s->models_id = $warehouseShoes->models_id;
        $s->models_id = $warehouseShoes->models_id;
    }

    /**
     * Handle the WarehouseShoes "updated" event.
     */
    public function updated(WarehouseShoes $warehouseShoes): void
    {
        //
    }

    /**
     * Handle the WarehouseShoes "deleted" event.
     */
    public function deleted(WarehouseShoes $warehouseShoes): void
    {
        //
    }

    /**
     * Handle the WarehouseShoes "restored" event.
     */
    public function restored(WarehouseShoes $warehouseShoes): void
    {
        //
    }

    /**
     * Handle the WarehouseShoes "force deleted" event.
     */
    public function forceDeleted(WarehouseShoes $warehouseShoes): void
    {
        //
    }
}
