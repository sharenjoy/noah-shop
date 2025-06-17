<?php

namespace Sharenjoy\NoahShop\Resources\Survey\SurveyResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Sharenjoy\NoahShop\Resources\Survey\SurveyResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahEditRecord;

class EditSurvey extends EditRecord
{
    use NoahEditRecord;

    protected static string $resource = SurveyResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
