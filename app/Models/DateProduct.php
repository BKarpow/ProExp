<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\BarcodeHandle;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;

class DateProduct extends Model
{
    /** @use HasFactory<\Database\Factories\DateProductFactory> */
    use HasFactory;
    use BarcodeHandle;

    const KEY_CACHE = "dateProduct_exps";
    const KEY_CACHE2 = "dateProduct_exps2";
    const KEY_CACHE3 = "dateProduct_exps3";

    protected $fillable = [
        'group_id',
        'user_id',
        'product_id',
        'count',
        'start',
        'end',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'start' => 'date',
        'end' => 'date',
        // або 'datetime', якщо там є час
    ];

    private bool $isImport = false;

    public function setImportMode():void
    {
        $this->isImport = true;
    }

    /**
     * Отримуємо кількість днів до дати.
     */
    protected function days(): Attribute
    {
        return Attribute::make(
            get: function () {
                // today() повертає 00:00 сьогоднішнього дня
                // diffInDays повертає різницю у цілих днях
                return (int) now()->startOfDay()->diffInDays($this->end, false);
            },
        );
    }

    public function isImportMode(): bool
    {
       return $this->isImport;
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    // public function productAlias()
    // {
    //     return $this->hasOne(NameProductUserAlias::class, 'product_id', 'product_id');
    // }

    public function productName($uid = null):string
    {
        $p = NameProductUserAlias::where([
            ['user_id', '=',(!$uid) ? Auth::id() : $uid],
            ['product_id', '=', $this->product_id]
        ])->first();
        if(!$p) {
            return $this->product->name;
        }
        return $p->name;
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }



    public function group()
    {
        return $this->belongsTo(User::class, 'id', 'group_id');
    }

    public function is25()
    {

        return $this->days_remaining <= (int)$this->user->configDefaultDaysex();
    }

    public function getEndDate() {
        return date('d.m.y', strtotime($this->end));
    }

    public function barcodeSvgUrl()
    {
        return $this->saveBarcodeToFile($this->product->barcode);
    }

    public function pathScreens()
    {
        return $this->hasMany(ScreenDateProduct::class, 'id', 'date_id' );
    }
}
