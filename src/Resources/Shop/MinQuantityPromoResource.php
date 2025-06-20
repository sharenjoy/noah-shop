<?php

namespace Sharenjoy\NoahShop\Resources\Shop;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Sharenjoy\NoahShop\Enums\PromoType;
use Sharenjoy\NoahShop\Models\MinQuantityPromo;
use Sharenjoy\NoahShop\Resources\Shop\MinQuantityPromoResource\Pages;
use Sharenjoy\NoahShop\Resources\Shop\Traits\PromoableResource;

class MinQuantityPromoResource extends Resource implements HasShieldPermissions
{
    use PromoableResource;

    protected static ?string $model = MinQuantityPromo::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?int $navigationSort = 26;

    public static function getModelLabel(): string
    {
        return __('noah-cms::noah-cms.min_quantity_promo');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereType(PromoType::MinQuantity->value);
    }

    protected static function getPromoFormSchema(): array
    {
        return [
            Section::make('滿件折扣設定')
                ->schema([
                    Hidden::make('type')->default(PromoType::MinQuantity->value),
                    TextInput::make('min_quantity')
                        ->label(__('noah-shop::noah-shop.shop.promo.title.min_quantity'))
                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.min_quantity')))
                        ->prefixIcon('heroicon-o-gift')
                        ->suffix('件')
                        ->placeholder(1000)
                        ->minValue(1)
                        ->numeric()
                        ->required()
                        ->rules(['required', 'numeric', 'min:1']),
                ]),
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
