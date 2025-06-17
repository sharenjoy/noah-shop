<?php

namespace Sharenjoy\NoahShop\Resources\Survey\AnswerResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Sharenjoy\NoahShop\Resources\Survey\AnswerResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahCreateRecord;

class CreateAnswer extends CreateRecord
{
    use NoahCreateRecord;

    protected static string $resource = AnswerResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
