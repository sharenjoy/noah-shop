<?php

namespace Sharenjoy\NoahShop\Resources\Survey\AnswerResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Sharenjoy\NoahShop\Resources\Survey\AnswerResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahEditRecord;

class EditAnswer extends EditRecord
{
    use NoahEditRecord;

    protected static string $resource = AnswerResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
