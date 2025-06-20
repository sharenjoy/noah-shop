<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Sharenjoy\NoahShop\Actions\Shop\OrderStatusRedirector;
use Sharenjoy\NoahShop\Actions\Shop\OrderStatusUpdater;
use Sharenjoy\NoahShop\Enums\OrderStatus;
use Sharenjoy\NoahShop\Models\BaseOrder;
use Sharenjoy\NoahShop\Models\User;
use Sharenjoy\NoahShop\Resources\Shop\ShippableOrderResource;

class UpdateOrderStatusAction
{
    public static function make(BaseOrder $order = null)
    {
        return Action::make('updateOrderStatusAction')
            ->label('更新訂單狀態')
            ->modalHeading('更新訂單狀態')
            ->color('primary')
            ->icon('heroicon-o-arrows-right-left')
            ->form([
                Section::make('訂單狀態')
                    ->extraAttributes(['style' => 'background-color: #f8f8f8'])
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                Select::make('status')
                                    ->label(__('noah-cms::noah-cms.order_status'))
                                    ->options(OrderStatus::visibleOptions())
                                    ->required(),

                                Textarea::make('content')->rows(2)->label(__('noah-cms::noah-cms.activity.label.status_notes')),
                            ]),
                    ]),
            ])
            ->mountUsing(function (ComponentContainer $form, $record) {
                $form->fill($record->toArray());
            })
            ->action(function (array $data, $record) {

                $statusEnum = OrderStatus::tryFrom($data['status']);

                if (! $statusEnum) {
                    Notification::make()
                        ->title('無效的訂單狀態')
                        ->danger()
                        ->send();
                    return;
                }

                $result = OrderStatusUpdater::run($record, $statusEnum, $data['content'] ?? null);

                if ($result === true) {

                    if ($statusEnum == OrderStatus::Processing && $record->getCurrentScope() == 'shippable') {
                        Notification::make()
                            ->danger()
                            ->title('可出貨訂單')
                            ->body($record->sn . ' 已更新為可出貨訂單，請點擊下方按鈕查看訂單資訊')
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('View')->url(ShippableOrderResource::getUrl('view', [
                                    'record' => $record->id,
                                ])),
                            ])
                            ->sendToDatabase(User::getCanHandleShippableUsers());
                    }

                    return OrderStatusRedirector::run($record);
                }
            })
            ->requiresConfirmation();
    }
}
