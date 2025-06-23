<?php

namespace Sharenjoy\NoahShop\Utils\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Sharenjoy\NoahCms\Utils\Tables\TableAbstract;
use Sharenjoy\NoahCms\Utils\Tables\TableInterface;

class OrderSn extends TableAbstract implements TableInterface
{
    public function make()
    {
        return TextColumn::make('sn')
            ->html()
            ->sortable()
            ->size(TextColumnSize::Medium)
            ->label(__('noah-shop::noah-shop.order_sn'))
            ->toggleable(isToggledHiddenByDefault: $this->content['isToggledHiddenByDefault'] ?? false);
    }
}
