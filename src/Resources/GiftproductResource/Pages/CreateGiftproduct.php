<?php

namespace Sharenjoy\NoahShop\Resources\GiftproductResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahShop\Resources\GiftproductResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahCreateRecord;

class CreateGiftproduct extends CreateRecord
{
    use NoahCreateRecord;

    protected static string $resource = GiftproductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_at'] = now();
        $data['updated_at'] = now();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
