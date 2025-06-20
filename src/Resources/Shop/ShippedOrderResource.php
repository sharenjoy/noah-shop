<?php

namespace Sharenjoy\NoahShop\Resources\Shop;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Sharenjoy\NoahShop\Models\ShippedOrder;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\ViewOrderInfoListAction;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\ViewOrderPickingListAction;
use Sharenjoy\NoahShop\Resources\Shop\Traits\OrderableResource;

class ShippedOrderResource extends Resource implements HasShieldPermissions
{
    use OrderableResource;

    protected static ?string $model = ShippedOrder::class;

    protected static ?int $navigationSort = 12;

    public static function getModelLabel(): string
    {
        return __('noah-cms::noah-cms.shipped_order');
    }

    public static function getBulkActions(): array
    {
        return [
            ViewOrderInfoListAction::make(resource: 'shipped-orders', actionType: 'bulk'),
            ViewOrderPickingListAction::make(resource: 'shipped-orders', actionType: 'bulk'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->shipped()->count();
    }

    public static function getPages(): array
    {
        return [
            'view' => Pages\Shipped\ViewOrder::route('/{record}'),
            'index' => Pages\Shipped\ListOrders::route('/'),
            'info-list' => Pages\Shipped\ViewOrderInfoList::route('/{record}/info-list'),
            'picking-list' => Pages\Shipped\ViewOrderPickingList::route('/{record}/picking-list'),
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
