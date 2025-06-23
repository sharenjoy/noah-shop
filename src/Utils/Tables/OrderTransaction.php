<?php

namespace Sharenjoy\NoahShop\Utils\Tables;

use Illuminate\Database\Eloquent\Builder;
use Sharenjoy\NoahShop\Tables\Columns\OrderTransactionColumn;
use Sharenjoy\NoahCms\Utils\Tables\TableAbstract;
use Sharenjoy\NoahCms\Utils\Tables\TableInterface;

class OrderTransaction extends TableAbstract implements TableInterface
{
    public function make()
    {
        return OrderTransactionColumn::make('transaction')
            ->searchable(query: function (Builder $query, string $search): Builder {
                return $query->whereHas('transaction', function ($query) use ($search) {
                    $query->where('sn', 'like', "%{$search}%")
                        ->orWhere('provider', 'like', "%{$search}%")
                        ->orWhere('payment_method', 'like', "%{$search}%")
                        ->orWhere('atm_code', 'like', "%{$search}%");
                });
            })
            ->label($this->getLabel($this->fieldName, $this->content))
            ->toggleable(isToggledHiddenByDefault: $this->content['isToggledHiddenByDefault'] ?? false);
    }
}
