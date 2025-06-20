<?php

namespace Sharenjoy\NoahShop\Resources\Shop;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Sharenjoy\NoahShop\Models\IssuedOrder;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\ViewOrderInfoListAction;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\ViewOrderPickingListAction;
use Sharenjoy\NoahShop\Resources\Shop\Traits\OrderableResource;

class IssuedOrderResource extends Resource implements HasShieldPermissions
{
    use OrderableResource;

    protected static ?string $model = IssuedOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';

    protected static ?int $navigationSort = 15;

    public static function getModelLabel(): string
    {
        return __('noah-cms::noah-cms.issued_order');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->issued()->count();
    }

    public static function getBulkActions(): array
    {
        return [
            ViewOrderInfoListAction::make(resource: 'issued-orders', actionType: 'bulk'),
            ViewOrderPickingListAction::make(resource: 'issued-orders', actionType: 'bulk'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'view' => Pages\Issued\ViewOrder::route('/{record}'),
            'index' => Pages\Issued\ListOrders::route('/'),
            'info-list' => Pages\Issued\ViewOrderInfoList::route('/{record}/info-list'),
            'picking-list' => Pages\Issued\ViewOrderPickingList::route('/{record}/picking-list'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
        ];
    }
}
