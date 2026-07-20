<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'barcode',
    ];

    public function groups()
    {
        return $this->belongsTo(GroupProduct::class, 'group_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(ImageProduct::class, 'product_id', 'id');
    }

    public function getProductImageUrl(bool $pathAbsolute = false)
    {
        $extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $disk = Storage::disk('public');
        $directory = 'products/';

        foreach ($extensions as $ext) {
            $path = "{$directory}product_{$this->barcode}.{$ext}";

            if ($disk->exists($path)) {
                // Повертаємо повний URL до файлу
                return ($pathAbsolute) ? $path : $disk->url($path);
            }
        }

        // Якщо нічого не знайдено, повертаємо посилання на "заглушку"
        return ($pathAbsolute) ? false : asset('storage/products/no-image.png');
    }

    public function mainImg()
    {
//        $fn = "products/product_" . $this->barcode . '.jpg';
        return $this->getProductImageUrl();
    }

    public function deleteMainImage(): bool
    {
        $mainFile = $this->getProductImageUrl(true);
        if ($mainFile) {
            Storage::disk('public')->delete($mainFile);
            return true;
        }
        return false;
    }


    public function productName($uid = null):string
    {
        $p = NameProductUserAlias::where([
            ['user_id', '=',(!$uid) ? Auth::id() : $uid],
            ['product_id', '=', $this->id]
        ])->first();
        if(!$p) {
            return $this->name;
        }
        return $p->name;
    }
}
