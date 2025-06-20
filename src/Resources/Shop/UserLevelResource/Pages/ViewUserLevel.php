<?php

namespace Sharenjoy\NoahShop\Resources\Shop\UserLevelResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Sharenjoy\NoahShop\Resources\Shop\UserLevelResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahViewRecord;

class ViewUserLevel extends ViewRecord
{
    use NoahViewRecord;

    protected static string $resource = UserLevelResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
