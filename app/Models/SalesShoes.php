<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesShoes extends Model
{
    use HasFactory;

    protected $table = 'sales_shoes';

    protected $fillable = [
        'models_id',
        'user_id',
        'size',
        'price',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'price'  => 'integer',
    ];

    // Зв'язок із моделлю взуття
    public function model(): BelongsTo
    {
        return $this->belongsTo(ModelsShoes::class, 'models_id');
    }

    // Зв'язок із користувачем
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
