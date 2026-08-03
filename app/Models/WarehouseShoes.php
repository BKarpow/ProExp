<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseShoes extends Model
{
    use HasFactory;

    protected $table = 'warehouse_shoes';

    protected $fillable = [
        'group_id',
        'models_id',
        'sizes',
        'residual',
        'price',
        'active',
    ];

    protected $casts = [
        'sizes' => 'array',  // Автоматично конвертує JSON у масив PHP та навпаки
        'active' => 'boolean',
    ];

    // Зв'язок з групою взуття
    public function group()
    {
        return $this->belongsTo(GroupShoes::class, 'group_id');
    }

    // Зв'язок з моделлю взуття
    public function model()
    {
        return $this->belongsTo(ModelsShoes::class, 'models_id');
    }
}