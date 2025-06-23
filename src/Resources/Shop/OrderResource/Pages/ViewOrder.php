<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages;

use Coolsam\NestedComments\Filament\Actions\CommentsAction;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\EditInvoiceAction;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\EditShipmentAction;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\EditTransactionAction;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\UpdateOrderStatusAction;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\ViewOrderInfoListAction;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\ViewOrderPickingListAction;
use Sharenjoy\NoahCms\Resources\Traits\NoahViewRecord;

class ViewOrder extends ViewRecord
{
    use NoahViewRecord;

    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CommentsAction::make()
                ->badgeColor('danger')
                ->label(__('noah-shop::noah-shop.comments'))
                ->badge(fn(Model $record) => $record->getAttribute('comments_count')),
            ActionGroup::make([
                UpdateOrderStatusAction::make(order: $this->record),
                EditShipmentAction::make(order: $this->record),
                EditInvoiceAction::make(order: $this->record),
                EditTransactionAction::make(order: $this->record),
                ViewOrderInfoListAction::make('orders'),
                ViewOrderPickingListAction::make('orders'),
            ])
                ->label('更多操作')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('primary')
                ->button(),
        ];
    }
}
