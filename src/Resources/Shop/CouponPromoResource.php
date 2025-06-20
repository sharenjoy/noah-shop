<?php

namespace Sharenjoy\NoahShop\Resources\Shop;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Sharenjoy\NoahShop\Actions\Shop\GetDeCryptExtendCondition;
use Sharenjoy\NoahShop\Enums\PromoAutoGenerateType;
use Sharenjoy\NoahShop\Enums\PromoType;
use Sharenjoy\NoahShop\Models\CouponPromo;
use Sharenjoy\NoahShop\Models\Promo;
use Sharenjoy\NoahShop\Resources\Shop\CouponPromoResource\Pages;
use Sharenjoy\NoahShop\Resources\Shop\CouponPromoResource\RelationManagers\UserCouponsRelationManager;
use Sharenjoy\NoahShop\Resources\Shop\Traits\PromoableResource;

class CouponPromoResource extends Resource implements HasShieldPermissions
{
    use PromoableResource;

    protected static ?string $model = CouponPromo::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?int $navigationSort = 20;

    public static function getModelLabel(): string
    {
        return __('noah-cms::noah-cms.coupon_promo');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereType(PromoType::Coupon->value);
    }

    protected static function getPromoFormSchema(): array
    {
        return [
            Section::make('折扣碼設定')
                ->schema([
                    Hidden::make('type')->default(PromoType::Coupon->value),
                    TextInput::make('code')
                        ->label(__('noah-shop::noah-shop.shop.promo.title.code'))
                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.code')))
                        ->prefixIcon('heroicon-o-gift')
                        ->placeholder('生日券')
                        ->required()
                        ->rules(['required', 'regex:/^[^\s\p{P}\p{S}]+$/u'])
                        ->maxLength(50)
                        ->unique(Promo::class, 'code', ignoreRecord: true),
                    TextInput::make('usage_limit')
                        ->label(__('noah-shop::noah-shop.shop.promo.title.usage_limit'))
                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.usage_limit')))
                        ->prefixIcon('heroicon-o-arrow-trending-up')
                        ->suffix('次')
                        ->placeholder(1000)
                        ->minValue(1)
                        ->numeric()
                        ->rules(['numeric', 'min:1']),
                    TextInput::make('per_user_limit')
                        ->label(__('noah-shop::noah-shop.shop.promo.title.per_user_limit'))
                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.per_user_limit')))
                        ->prefixIcon('heroicon-o-arrow-trending-up')
                        ->suffix('次')
                        ->placeholder(3)
                        ->minValue(1)
                        ->numeric()
                        ->rules(['numeric', 'min:1']),
                    Radio::make('auto_assign_to_user')
                        ->label(__('noah-shop::noah-shop.shop.promo.title.auto_assign_to_user'))
                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.auto_assign_to_user')))
                        ->options([
                            true => __('noah-cms::noah-cms.yes'),
                            false => __('noah-cms::noah-cms.no'),
                        ])
                        ->default(false)
                        ->inline()
                        ->inlineLabel(false),
                ]),
            Section::make('折扣碼事件設定')
                ->visible(fn(Get $get): bool => $get('forever'))
                ->schema([
                    Select::make('auto_generate_type')
                        ->label(__('noah-shop::noah-shop.shop.promo.title.auto_generate_type'))
                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.auto_generate_type')))
                        ->options(PromoAutoGenerateType::class)
                        ->default(PromoAutoGenerateType::Never->value)
                        ->required()
                        ->live(),
                    DatePicker::make('auto_generate_date')
                        ->label(__('noah-shop::noah-shop.shop.promo.title.auto_generate_date'))
                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.auto_generate_date')))
                        ->displayFormat('m月d日') // 顯示格式
                        ->placeholder('03月18日')
                        ->formatStateUsing(fn($state) => $state ? Carbon::parse((now()->format('Y') . '-' . $state))->format('Y-m-d') : null)
                        ->dehydrateStateUsing(fn($state) => $state ? Carbon::parse($state)->format('m-d') : null) // 儲存成完整日期
                        ->required()
                        ->native(false)
                        ->closeOnDateSelection()
                        ->visible(fn(Get $get): bool => $get('auto_generate_type') == 'yearly'),
                    Select::make('auto_generate_day')
                        ->label('日期')
                        ->label(__('noah-shop::noah-shop.shop.promo.title.auto_generate_day'))
                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.auto_generate_day')))
                        ->options(collect(range(1, 31))->mapWithKeys(fn($i) => [$i => $i]))
                        ->required()
                        ->visible(fn(Get $get): bool => $get('auto_generate_type') == 'monthly'),
                    TextInput::make('coupon_valid_days')
                        ->label(__('noah-shop::noah-shop.shop.promo.title.coupon_valid_days'))
                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.coupon_valid_days')))
                        ->prefixIcon('heroicon-o-calendar')
                        ->suffix('天')
                        ->placeholder(30)
                        ->minValue(1)
                        ->numeric()
                        ->required()
                        ->rules(['required', 'numeric', 'min:1']),
                ]),
        ];
    }

    public static function getRelations(): array
    {
        return [
            UserCouponsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromos::route('/'),
            'create' => Pages\CreatePromo::route('/create'),
            'edit' => Pages\EditPromo::route('/{record}/edit'),
            'view' => Pages\ViewPromo::route('/{record}'),
        ];
    }
}
