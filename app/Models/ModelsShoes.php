<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelsShoes extends Model
{
    /** @use HasFactory<\Database\Factories\ModelsShoesFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'desc',
    ];
}
