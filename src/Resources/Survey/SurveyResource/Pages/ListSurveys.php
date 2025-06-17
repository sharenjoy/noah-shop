<?php

namespace Sharenjoy\NoahShop\Resources\Survey\SurveyResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Sharenjoy\NoahShop\Resources\Survey\SurveyResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahListRecords;

class ListSurveys extends ListRecords
{
    use NoahListRecords;

    protected static string $resource = SurveyResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
