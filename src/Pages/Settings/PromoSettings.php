<?php

namespace Sharenjoy\NoahShop\Pages\Settings;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Closure;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;
use Sharenjoy\NoahCms\Actions\Shop\ShopFeatured;

class PromoSettings extends BaseSettings
{
    use HasPageShield;

    protected static ?int $navigationSort = 76;

    protected static ?string $navigationIcon = null;

    public static function getNavigationGroup(): string
    {
        return __('noah-shop::noah-shop.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('noah-shop::noah-shop.promo');
    }

    public function schema(): array|Closure
    {
        return [
            Section::make('促銷相關')
                ->visible(fn(): bool => ShopFeatured::run('shop'))
                ->schema([
                    Section::make('折扣設定')->schema([
                        Radio::make('shop.decimal_point_calculate_type')
                            ->label(__('noah-shop::noah-shop.shop.promo.title.decimal_point_calculate_type'))
                            ->options([
                                'floor' => '無條件捨去',
                                'ceil' => '無條件進位',
                                'round' => '四捨五入',
                            ])
                            ->inline()
                            ->inlineLabel(false),
                        Fieldset::make('百分比折抵方式')
                            ->columns(1)
                            ->schema([
                                Radio::make('shop.discount_percent_amount_type')
                                    ->label(__('noah-shop::noah-shop.shop.promo.title.discount_percent_amount_type'))
                                    ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.discount_percent_amount_type')))
                                    ->options([
                                        'entire' => '整筆訂單金額計算折抵',
                                        'product' => '只有符合商品合計金額計算折抵',
                                    ])
                                    ->inline()
                                    ->inlineLabel(false)
                                    ->live(),
                                Radio::make('shop.discount_percent_calculate_type')
                                    ->label(__('noah-shop::noah-shop.shop.promo.title.discount_percent_calculate_type'))
                                    ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.discount_percent_calculate_type')))
                                    ->options([
                                        'combined' => '疊加折抵',
                                        'devided' => '分開折抵',
                                    ])
                                    ->inline()
                                    ->inlineLabel(false)
                                    ->visible(fn(Get $get): bool => $get('shop.discount_percent_amount_type') == 'entire'),
                            ]),
                    ]),
                ]),
        ];
    }
}
