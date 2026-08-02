<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupShoes extends Model
{
    /** @use HasFactory<\Database\Factories\GroupShoesFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'desc',
    ];
}
