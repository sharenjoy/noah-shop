<?php

namespace Sharenjoy\NoahShop\Resources\Shop\ObjectiveResource\Pages;

use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Sharenjoy\NoahShop\Enums\ObjectiveType;
use Sharenjoy\NoahShop\Models\Objective;
use Sharenjoy\NoahShop\Resources\Shop\ObjectiveResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahListRecords;

class ListObjectives extends ListRecords
{
    use NoahListRecords;

    protected static string $resource = ObjectiveResource::class;

    public function getTabs(): array
    {
        $tabs = [];

        $tabs['all'] = Tab::make('ALL')
            ->badge(fn() => Objective::all()->count())
            ->label('ALL')
            ->modifyQueryUsing(fn(Builder $query) => $query)
            ->icon('');

        foreach (ObjectiveType::cases() as $case) {
            $tabs[$case->value] = Tab::make($case->getLabel())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', $case->value))
                ->badge(fn() => Objective::where('type', $case->value)->count());
        }

        return $tabs;
    }

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
