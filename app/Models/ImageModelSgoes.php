<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageModelSgoes extends Model
{
    /** @use HasFactory<\Database\Factories\ImageModelSgoesFactory> */
    use HasFactory;

    protected $fillable = [
            'images', 'active', 'desc'
            // інші поля...
        ];

    protected function casts(): array
        {
            return [
                'images' => 'array', // Автоматично конвертує JSON з БД у PHP-масив і навпаки
                'active' => 'boolean',
            ];
        }
}
