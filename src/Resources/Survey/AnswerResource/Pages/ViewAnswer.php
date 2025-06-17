<?php

namespace Sharenjoy\NoahShop\Resources\Survey\AnswerResource\Pages;

use Sharenjoy\NoahShop\Resources\Survey\AnswerResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahViewRecord;
use Filament\Resources\Pages\ViewRecord;

class ViewAnswer extends ViewRecord
{
    use NoahViewRecord;

    protected static string $resource = AnswerResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
