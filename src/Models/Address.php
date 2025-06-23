<?php

namespace Sharenjoy\NoahShop\Models;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sharenjoy\NoahShop\Actions\Shop\FetchAddressRelatedSelectOptions;
use Sharenjoy\NoahShop\Actions\Shop\FetchCountryRelatedSelectOptions;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class Address extends Model
{
    use CommonModelTrait;
    use HasFactory;
    use LogsActivity;

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public $translatable = [];

    protected array $sort = [
        'is_default' => 'desc',
        'created_at' => 'desc',
    ];

    protected static function booted()
    {
        static::saved(function (Address $address) {
            // 如果 is_default 為 true，將同一 user 的其他地址的 is_default 設為 false
            if ($address->is_default) {
                Address::where('user_id', $address->user_id)
                    ->where('id', '!=', $address->id) // 排除當前地址
                    ->update(['is_default' => false]);
            }
        });
    }

    protected function formFields(): array
    {
        return [
            'left' => [
                'country' => Select::make('country')
                    ->label(__('noah-shop::noah-shop.activity.label.country'))
                    ->options(FetchCountryRelatedSelectOptions::run('country'))
                    ->searchable()
                    ->required()
                    ->live(),
                'postcode' => TextInput::make('postcode')
                    ->label(__('noah-shop::noah-shop.activity.label.postcode'))
                    ->placeholder('100')
                    ->required(),
                'city' => Select::make('city')
                    ->label(__('noah-shop::noah-shop.activity.label.city'))
                    ->visible(fn(Get $get): bool => $get('country') == 'Taiwan')
                    ->placeholder('台北市')
                    ->options(FetchAddressRelatedSelectOptions::run('city'))
                    ->searchable()
                    ->required()
                    ->live(),
                'district' => Select::make('district')
                    ->label(__('noah-shop::noah-shop.activity.label.district'))
                    ->options(fn(Get $get) => FetchAddressRelatedSelectOptions::run('district', $get('city')))
                    ->placeholder('中正區')
                    ->searchable()
                    ->required()
                    ->visible(fn(Get $get): bool => $get('country') == 'Taiwan'),
                'address' => Textarea::make('address')
                    ->columnSpanFull()
                    ->label(__('noah-shop::noah-shop.activity.label.address'))
                    ->placeholder('中正路1號')
                    ->rows(2)
                    ->required(),
            ],
            'right' => [
                'is_default' => ['alias' => 'yesno'],
            ],
        ];
    }

    protected function tableFields(): array
    {
        return [
            'country' => ['label' => 'activity.label.country'],
            'postcode' => ['label' => 'activity.label.postcode'],
            'city' => ['label' => 'activity.label.city'],
            'district' => ['label' => 'activity.label.district'],
            'address' => ['label' => 'activity.label.address'],
            'is_default' => ['type' => 'boolean'],
            'created_at' => ['isToggledHiddenByDefault' => true],
            'updated_at' => ['isToggledHiddenByDefault' => true],
        ];
    }

    /** RELACTIONS */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** SCOPES */

    /** EVENTS */

    /** SEO */

    /** OTHERS */

    protected static function newFactory()
    {
        return \Sharenjoy\NoahShop\Database\Factories\AddressFactory::new();
    }
}
