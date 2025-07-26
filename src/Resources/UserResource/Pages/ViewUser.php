<?php

namespace Sharenjoy\NoahShop\Resources\UserResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Sharenjoy\NoahCms\Resources\Traits\NoahViewRecord;
use Sharenjoy\NoahShop\Resources\UserResource;
use Sharenjoy\NoahShop\Resources\UserResource\Actions\UpdateUserPasswordAction;

class ViewUser extends ViewRecord
{
    use NoahViewRecord;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([
            UpdateUserPasswordAction::make(),
        ], $this->recordHeaderActions());
    }
}
