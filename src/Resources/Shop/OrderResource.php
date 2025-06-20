<?php

namespace Sharenjoy\NoahShop\Resources\Shop;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Sharenjoy\NoahShop\Models\Order;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\ViewOrderInfoListAction;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\ViewOrderPickingListAction;
use Sharenjoy\NoahShop\Resources\Shop\Traits\OrderableResource;

class OrderResource extends Resource implements HasShieldPermissions
{
    use OrderableResource;

    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('noah-cms::noah-cms.order');
    }

    public static function getBulkActions(): array
    {
        return [
            ViewOrderInfoListAction::make(resource: 'orders', actionType: 'bulk'),
            ViewOrderPickingListAction::make(resource: 'orders', actionType: 'bulk'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'info-list' => Pages\ViewOrderInfoList::route('/{record}/info-list'),
            'picking-list' => Pages\ViewOrderPickingList::route('/{record}/picking-list'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
        ];
    }
}
