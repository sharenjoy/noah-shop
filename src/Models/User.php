<?php

namespace Sharenjoy\NoahShop\Models;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Sharenjoy\NoahCms\Actions\GenerateUserSeriesNumber;
use Sharenjoy\NoahCms\Models\Role;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahCms\Models\Traits\HasTags;
use Sharenjoy\NoahShop\Actions\Shop\FetchCountryRelatedSelectOptions;
use Sharenjoy\NoahCms\Actions\Shop\RoleCan;
use Sharenjoy\NoahCms\Actions\Shop\ShopFeatured;
use Sharenjoy\NoahShop\Enums\ObjectiveType;
use Sharenjoy\NoahShop\Enums\UserLevelStatus as EnumsUserLevelStatus;
use Sharenjoy\NoahShop\Models\Address;
use Sharenjoy\NoahShop\Models\Objective;
use Sharenjoy\NoahShop\Models\Order;
use Sharenjoy\NoahShop\Models\Traits\HasCoin;
use Sharenjoy\NoahShop\Models\UserCoupon;
use Sharenjoy\NoahShop\Models\UserCouponStatus;
use Sharenjoy\NoahShop\Models\UserLevel;
use Sharenjoy\NoahShop\Models\UserLevelStatus;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use CommonModelTrait;
    use HasFactory;
    use LogsActivity;
    use Notifiable;
    use SoftDeletes;
    use HasRoles;
    use HasTags;
    use HasCoin;

    protected $fillable = [
        'name',
        'email',
        'password',
        'sn',
        'calling_code',
        'mobile',
        'address',
        'birthday',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'age',
    ];

    public $translatable = [];

    protected array $sort = [
        'created_at' => 'desc',
        'id' => 'desc',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->sn) {
                $model->sn = GenerateUserSeriesNumber::run('M');
            }

            $userLevel = UserLevel::where('is_default', true)->first();
            if ($userLevel && !$model->user_level_id) {
                $model->user_level_id = $userLevel->id;
            }
        });

        // 用戶建立後將預設的會員等級指定給會員
        static::created(function ($model) {
            if ($model->user_level_id) {
                $userLevel = UserLevel::find($model->user_level_id);
                // 創建用戶等級狀態
                $model->userLevelStatuses()->create([
                    'user_level_id' => $userLevel->id,
                    'status' => EnumsUserLevelStatus::On->value,
                    'started_at' => now(),
                    'expired_at' => now()->addYears($userLevel->level_duration ?? 100)->endOfDay(), // 設置過期時間，且不設置過期時間的話，則預設為 100 年
                ]);
            }
        });

        // 當用戶更新時，如果會員等級資料有變更，則更新會員等級狀態
        static::updating(function ($model) {
            if ($model->isDirty('user_level_id')) {
                $userLevelStatus = $model->userLevelStatuses()->get();
                // 如果有會員等級狀態，則將其狀態設置為 Off
                foreach ($userLevelStatus as $status) {
                    $status->update([
                        'status' => EnumsUserLevelStatus::Off->value,
                    ]);
                }
                // 這裡的邏輯是將所有會員等級狀態設置為 Off，然後再創建一個新的會員等級狀態
                $model->userLevelStatuses()->create([
                    'user_level_id' => $model->user_level_id,
                    'status' => EnumsUserLevelStatus::On->value,
                    'started_at' => now(),
                    'expired_at' => now()->addYears($model->userLevel->level_duration ?? 100)->endOfDay(), // 設置過期時間，且不設置過期時間的話，則預設為 100 年
                ]);
            }
        });
    }

    protected function formFields(): array
    {
        return [
            'left' => [
                'user_level_id' => Section::make()->schema([
                    Select::make('user_level_id')
                        ->label(__('noah-shop::noah-shop.user_level'))
                        ->helperText(new HtmlString(__('noah-shop::noah-shop.super_admin_only')))
                        ->relationship('userLevel', 'title')
                        // ->searchable()
                        ->required()
                        // 只有最高權限角色可以編輯
                        ->disabled(fn(Get $get): bool => $get('id') && !RoleCan::run(role: 'super_admin')),
                ])->visible(fn(Get $get): bool => (bool)$get('id') && ShopFeatured::run('shop')),
                'name' => [
                    'required' => true,
                    'rules' => ['required', 'string'],
                ],
                'email' => [
                    'alias' => 'user_email',
                    'required' => true,
                    'rules' => ['required', 'email'],
                ],
                'password' => Section::make()->schema([
                    TextInput::make('password')
                        ->label(__('noah-shop::noah-shop.password'))
                        ->placeholder('********')
                        ->password()
                        ->dehydrated(fn($state) => !empty($state))
                        ->required(fn(Get $get): bool => !$get('id'))
                        ->rules(['min:8']),
                ])->visible(fn(Get $get): bool => !$get('id')),
                'calling_code' => Section::make()
                    ->columns(2)
                    ->schema([
                        Select::make('calling_code')
                            ->label(__('noah-shop::noah-shop.activity.label.calling_code'))
                            ->options(FetchCountryRelatedSelectOptions::run('calling_code'))
                            ->searchable()
                            ->required(),
                        TextInput::make('mobile')->placeholder('0912345678')->label(__('noah-shop::noah-shop.activity.label.mobile'))->required(),
                    ]),
            ],
            'right' => [
                'birthday' => Section::make()->schema([
                    DatePicker::make('birthday')
                        ->label(__('noah-shop::noah-shop.shop.promo.title.birthday'))
                        ->placeholder('2020-03-18')
                        ->displayFormat('Y-m-d') // 顯示格式
                        ->prefixIcon('heroicon-o-calendar')
                        ->rules(['date'])
                        ->minDate(now()->subYears(100))
                        ->maxDate(now()->subYears(10))
                        ->formatStateUsing(fn($state) => $state ? Carbon::parse($state)->format('Y-m-d') : null)
                        ->dehydrateStateUsing(fn($state) => $state ? Carbon::parse($state)->format('Y-m-d') : null)
                        ->native(false)
                        ->closeOnDateSelection()
                ]),
                'tags' => ['min' => 0, 'max' => 3, 'multiple' => true],
            ],
        ];
    }

    protected function tableFields(): array
    {
        return [
            'sn' => [],
            'userLevel.title' =>  ['alias' => 'belongs_to', 'label' => 'user_level', 'relation' => 'userLevel', 'relation_route' => 'shop.user-levels', 'relation_column' => 'user_level_id', 'visible' => ShopFeatured::run('shop')],
            'name' => [],
            'email' => [],
            'user_coin' => ['label' => ShopFeatured::run('coin-shoppingmoney') ? 'user_coin' : 'user_point'],
            'roles' => [],
            'tags' => ['tagType' => 'user'],
            'created_at' => [],
            'updated_at' => [],
        ];
    }

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

    protected static function newFactory()
    {
        return \Sharenjoy\NoahShop\Database\Factories\UserFactory::new();
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function tags(): MorphToMany
    {
        return $this
            ->morphToMany(self::getTagClassName(), $this->getTaggableMorphName(), $this->getTaggableTableName())
            ->using($this->getPivotModelClassName())
            ->where('type', 'user')
            ->ordered();
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->establishedOrders();
    }

    public function validOrders(): HasMany
    {
        return $this->hasMany(Order::class)->validOrders();
    }

    public function objectives(): MorphToMany
    {
        return $this->morphToMany(Objective::class, 'objectiveable')->whereType(ObjectiveType::User->value);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(UserCoupon::class);
    }

    public function userLevel(): BelongsTo
    {
        return $this->belongsTo(UserLevel::class)->orderBy('order_column', 'asc');
    }

    public function userLevelStatuses(): HasMany
    {
        return $this->hasMany(UserLevelStatus::class);
    }

    public function userCouponStatuses(): HasMany
    {
        return $this->hasMany(UserCouponStatus::class);
    }

    public function scopeSuperAdmin($query)
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('name', 'super_admin');
        });
    }

    public function scopeWithRolesHavingPermissions($query, array $permissions): Builder
    {
        // 查詢同時擁有所有指定權限的角色名稱
        $roles = Role::whereHas('permissions', function ($q) use ($permissions) {
            $q->whereIn('name', $permissions);
        }, '=', count($permissions))->pluck('name');

        // 查詢擁有上述角色的使用者
        return $query->whereHas('roles', function ($q) use ($roles) {
            $q->whereIn('name', $roles);
        });
    }

    // 查詢擁有指定權限的使用者
    public static function getCanHandleShippableUsers()
    {
        return User::withRolesHavingPermissions([
            "view_any_shop::shippable::order",
            "view_shop::shippable::order"
        ])->get();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function age(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['birthday'] ? Carbon::parse($attributes['birthday'])->age : null,
        );
    }
}
