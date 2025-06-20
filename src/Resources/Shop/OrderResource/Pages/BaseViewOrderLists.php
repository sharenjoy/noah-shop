<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages;

use Filament\Resources\Pages\Page;

class BaseViewOrderLists extends Page
{
    public $models;
    public $ids;

    public function mount($record)
    {
        if (request()->has('ids')) {
            $this->ids = request()->get('ids');
        } else {
            $this->ids = [$record];
        }

        $this->models = static::$resource::getEloquentQuery()->whereIn('id', $this->ids)->get();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
