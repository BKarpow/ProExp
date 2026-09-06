<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    const ROLE_ADMIN = 77;
    const ROLE_MANAGER = 22;
    const CONF_KEY_SHOP = 'defaultShop';
    const CONF_KEY_GROUP = 'defaultGroup';
    const CONF_KEY_TG_CHAT_ID = 'telegramChatId';
    const CONF_KEY_EXPS_DAYS = 'expsDays';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getUsernameAttribute()
    {
        return $this->phone;
    }

    /**
     * Автоматична обробка номеру телефону.
     */
    protected function phone(): Attribute
    {
        return Attribute::make(
            // Геттер: форматуємо номер при читанні з бази для відображення
            get: fn($value) => $this->formatPhoneForDisplay($value),

            // Сеттер: очищаємо номер перед записом у базу
            set: fn($value) => preg_replace('/\D/', '', $value),
        );
    }

    /**
     * Допоміжний метод для красивого виводу
     */
    private function formatPhoneForDisplay($value)
    {
        if (strlen($value) == 12) {
            // Перетворює 380671234567 на +38 (067) 123-45-67
            return sprintf(
                "+%s (%s) %s-%s-%s",
                substr($value, 0, 2),
                substr($value, 2, 3),
                substr($value, 5, 3),
                substr($value, 8, 2),
                substr($value, 10, 2)
            );
        }
        return $value;
    }

    public function isAdmin(): bool
    {

        return (int)$this->role == self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {

        return (int)$this->role == self::ROLE_MANAGER;
    }

    public function groups()
    {
        return $this->hasMany(GroupProduct::class, 'user_id', 'id');
    }

    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class);
    }

    public function configs()
    {
        return $this->hasMany(ConfigsUser::class, 'user_id', 'id');
    }

    public function configDefaultShop()
    {
        return $this->configs()->select('value')->where('key', self::CONF_KEY_SHOP)
            ->first()->value ?? 0;
    }

    public function configDefaultGroup()
    {
        return $this->configs()->select('value')->where('key', self::CONF_KEY_GROUP)
            ->first()->value ?? 0;
    }

    public function configDefaultDaysex()
    {
        return $this->configs()->select('value')->where('key', self::CONF_KEY_EXPS_DAYS)
            ->first()->value ?? 7;
    }

    public function defaultShop()
    {
        $sid = $this->configDefaultShop();
        if ($sid != 0)
            return Shop::findOrFail($sid);
        return null;
    }

    public function exps()
    {
        return $this->hasMany(DateProduct::class, 'user_id', 'id')->whereRaw('`end` >= CURDATE()');
    }

    public function expiredDays()
    {
//        return Cache::remember(DateProduct::KEY_CACHE.$this->configDefaultGroup(), now()->addHours(8), function () {
            return DateProduct::query()
                ->select('*')
                ->selectRaw('DATEDIFF(end, CURDATE()) as days_remaining')
                ->orderBy('days_remaining', 'asc')
                ->where('group_id', (int)$this->configDefaultGroup())
                ->where('done', '=', false)
                ->having('days_remaining', '>=', 0)
                ->having('days_remaining', '<=', $this->configDefaultDaysex())
                ->limit(75)
                ->get();
//        });
        // return DateProduct::query()
        //     ->select('*')
        //     ->selectRaw('DATEDIFF(end, CURDATE()) as days_remaining')
        //     ->orderBy('days_remaining', 'asc')
        //     ->where('group_id', (int)$this->configDefaultGroup())
        //     ->having('days_remaining', '>=', 0)
        //     ->having('days_remaining', '<=', $this->configDefaultDaysex())
        //     ->get();
    }

    public function expProductsAll()
    {
        return Cache::remember(DateProduct::KEY_CACHE2.$this->configDefaultGroup(), now()->addHours(3), function () {

        return DateProduct::query()
            ->select('*')
            ->selectRaw('DATEDIFF(end, CURDATE()) as days_remaining')

            ->orderBy('days_remaining', 'asc')
            ->where('group_id', (int)$this->configDefaultGroup())
            ->where('done', '=', false)
            ->having('days_remaining', '>=', 0)
            ->paginate(100);
        });
        // return DateProduct::query()
        //     ->select('*')
        //     ->selectRaw('DATEDIFF(end, CURDATE()) as days_remaining')
        //     ->orderBy('days_remaining', 'asc')
        //     ->where('group_id', (int)$this->configDefaultGroup())
        //     ->having('days_remaining', '>=', 0)
        //     ->paginate(50);
    }

    public function beforeExpProductsAll()
    {
        return Cache::remember(DateProduct::KEY_CACHE3.$this->configDefaultGroup(), now()->addHours(24), function () {
            return DateProduct::query()
            ->select('*')
            ->selectRaw('DATEDIFF(end, CURDATE()) as days_remaining')
            ->orderBy('days_remaining', 'asc')
            ->where('group_id', (int)$this->configDefaultGroup())
            ->having('days_remaining', '<', 0)
            ->paginate(100);
        });
    }

    public function telegram()
    {
        return $this->hasOne(TelegramHandler::class, 'user_id', 'id');
    }

    public function isRegisterTelegram(): bool
    {
        return  false;
    }

    public function meta()
    {
        return $this->hasOne(UserMetaData::class, 'user_id', 'id');
    }
}
