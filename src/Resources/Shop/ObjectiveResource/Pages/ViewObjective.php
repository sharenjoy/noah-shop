<?php

namespace Sharenjoy\NoahShop\Resources\Shop\ObjectiveResource\Pages;

use Sharenjoy\NoahShop\Resources\Shop\ObjectiveResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahViewRecord;
use Filament\Resources\Pages\ViewRecord;

class ViewObjective extends ViewRecord
{
    use NoahViewRecord;

    protected static string $resource = ObjectiveResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
