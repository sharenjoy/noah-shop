<?php

namespace Sharenjoy\NoahShop\Utils\Tables;

use Illuminate\Database\Eloquent\Builder;
use Sharenjoy\NoahShop\Tables\Columns\OrderInvoiceColumn;
use Sharenjoy\NoahCms\Utils\Tables\TableAbstract;
use Sharenjoy\NoahCms\Utils\Tables\TableInterface;

class OrderInvoice extends TableAbstract implements TableInterface
{
    public function make()
    {
        return OrderInvoiceColumn::make('invoice')
            ->searchable(query: function (Builder $query, string $search): Builder {
                return $query->whereHas('invoice', function ($query) use ($search) {
                    $query->where('company_title', 'like', "%{$search}%")
                        ->orWhere('company_code', 'like', "%{$search}%")
                        ->orWhere('holder_code', 'like', "%{$search}%");
                });
            })
            ->label($this->getLabel($this->fieldName, $this->content))
            ->toggleable(isToggledHiddenByDefault: $this->content['isToggledHiddenByDefault'] ?? false);
    }
}
