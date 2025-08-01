<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Issued;

use Coolsam\NestedComments\Filament\Actions\CommentsAction;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahShop\Resources\Shop\IssuedOrderResource;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\UpdateOrderStatusAction;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\ViewOrderInfoListAction;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\ViewOrderPickingListAction;
use Sharenjoy\NoahCms\Resources\Traits\NoahViewRecord;

class ViewOrder extends ViewRecord
{
    use NoahViewRecord;

    protected static string $resource = IssuedOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CommentsAction::make()
                ->label(__('noah-shop::noah-shop.comments'))
                ->badgeColor('danger')
                ->badge(fn(Model $record) => $record->getAttribute('comments_count')),
            ActionGroup::make([
                UpdateOrderStatusAction::make(order: $this->record),
                ViewOrderInfoListAction::make(resource: 'issued-orders'),
                ViewOrderPickingListAction::make(resource: 'issued-orders'),
            ])
                ->label('更多操作')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('primary')
                ->button(),
        ];
    }
}
