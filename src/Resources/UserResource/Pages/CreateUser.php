<?php

namespace Sharenjoy\NoahShop\Resources\UserResource\Pages;

use Sharenjoy\NoahShop\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
