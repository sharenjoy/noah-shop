<?php

namespace Sharenjoy\NoahShop\Resources\Shop;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkAction;
use Sharenjoy\NoahShop\Enums\OrderShipmentStatus;
use Sharenjoy\NoahShop\Enums\OrderStatus;
use Sharenjoy\NoahShop\Models\ShippableOrder;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\ViewOrderInfoListAction;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\ViewOrderPickingListAction;
use Sharenjoy\NoahShop\Resources\Shop\Traits\OrderableResource;

class ShippableOrderResource extends Resource implements HasShieldPermissions
{
    use OrderableResource;

    protected static ?string $model = ShippableOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 8;

    public static function getModelLabel(): string
    {
        return __('noah-cms::noah-cms.shippable_order');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->shippable()->count();
    }

    public static function getBulkActions(): array
    {
        return [
            ViewOrderInfoListAction::make(resource: 'shippable-orders', actionType: 'bulk'),
            ViewOrderPickingListAction::make(resource: 'shippable-orders', actionType: 'bulk'),
            BulkAction::make('update_status_to_shipped')
                ->label('更新訂單狀態至已出貨')
                ->action(function ($records) {
                    foreach ($records as $record) {
                        $record->shipment->update(['status' => OrderShipmentStatus::Shipped]);
                    }
                })
                ->requiresConfirmation()
                ->color('danger'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'view' => Pages\Shippable\ViewOrder::route('/{record}'),
            'index' => Pages\Shippable\ListOrders::route('/'),
            'info-list' => Pages\Shippable\ViewOrderInfoList::route('/{record}/info-list'),
            'picking-list' => Pages\Shippable\ViewOrderPickingList::route('/{record}/picking-list'),
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
