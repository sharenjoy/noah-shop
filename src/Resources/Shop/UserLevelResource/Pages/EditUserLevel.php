<?php

namespace Sharenjoy\NoahShop\Resources\Shop\UserLevelResource\Pages;

use Sharenjoy\NoahShop\Resources\Shop\UserLevelResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahEditRecord;
use Filament\Resources\Pages\EditRecord;

class EditUserLevel extends EditRecord
{
    use NoahEditRecord;

    protected static string $resource = UserLevelResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
