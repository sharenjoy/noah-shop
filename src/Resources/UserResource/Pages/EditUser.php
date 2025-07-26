<?php

namespace Sharenjoy\NoahShop\Resources\UserResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Sharenjoy\NoahCms\Resources\Traits\NoahEditRecord;
use Sharenjoy\NoahShop\Resources\UserResource;
use Sharenjoy\NoahShop\Resources\UserResource\Actions\UpdateUserPasswordAction;

class EditUser extends EditRecord
{
    use NoahEditRecord;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([
            UpdateUserPasswordAction::make(),
        ], $this->recordHeaderActions());
    }
}
