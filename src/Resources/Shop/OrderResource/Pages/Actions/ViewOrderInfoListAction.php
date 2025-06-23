<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Tables\Actions\BulkAction;

class ViewOrderInfoListAction
{
    public static function make(string $resource, ?string $actionType = 'single')
    {
        if ($actionType === 'single') {
            return Action::make('view_order_info_list')
                ->label(__('noah-shop::noah-shop.view_order_info_list'))
                ->icon('heroicon-o-document-text')
                ->url(function ($record) use ($resource) {
                    return route('filament.' . Filament::getCurrentPanel()->getId() . '.resources.shop.' . $resource . '.info-list', [
                        'record' => $record,
                    ]);
                });
        }

        if ($actionType === 'bulk') {
            return BulkAction::make('view_order_info_list')
                ->label(__('noah-shop::noah-shop.view_order_info_list'))
                ->action(function ($records) use ($resource) {
                    return redirect()->route('filament.' . Filament::getCurrentPanel()->getId() . '.resources.shop.' . $resource . '.info-list', [
                        'record' => $records->first()->id,
                        'ids' => $records->pluck('id')->toArray(),
                    ]);
                })
                ->requiresConfirmation()
                ->color('primary');
        }
    }
}
