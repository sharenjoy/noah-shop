<?php

namespace Sharenjoy\NoahShop\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Sharenjoy\NoahCms\Resources\Traits\NoahListRecords;
use Sharenjoy\NoahShop\Resources\UserResource;

class ListUsers extends ListRecords
{
    use NoahListRecords;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
