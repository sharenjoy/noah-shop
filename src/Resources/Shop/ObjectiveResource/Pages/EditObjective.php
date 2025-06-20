<?php

namespace Sharenjoy\NoahShop\Resources\Shop\ObjectiveResource\Pages;

use Sharenjoy\NoahShop\Resources\Shop\ObjectiveResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahEditRecord;
use Filament\Resources\Pages\EditRecord;

class EditObjective extends EditRecord
{
    use NoahEditRecord;

    protected static string $resource = ObjectiveResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
