<?php

namespace Sharenjoy\NoahShop\Utils\Tables;

use Sharenjoy\NoahCms\Actions\Shop\ShopFeatured;
use Sharenjoy\NoahCms\Tables\Columns\UserCoinColumn;
use Sharenjoy\NoahCms\Utils\Tables\TableAbstract;
use Sharenjoy\NoahCms\Utils\Tables\TableInterface;

class UserCoin extends TableAbstract implements TableInterface
{
    public function make()
    {
        return UserCoinColumn::make('point')
            ->visible(fn() => ShopFeatured::run('coin-point') || ShopFeatured::run('coin-shoppingmoney'))
            ->label($this->getLabel($this->fieldName, $this->content))
            ->toggleable(isToggledHiddenByDefault: $this->content['isToggledHiddenByDefault'] ?? false);
    }
}
