<?php

namespace Sharenjoy\NoahShop\Resources\Survey\EntryResource\Pages;

use Coolsam\NestedComments\Filament\Actions\CommentsAction;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahShop\Resources\Survey\EntryResource;
use Sharenjoy\NoahShop\Resources\Survey\EntryResource\Pages\Actions\UpdateSurveyEntryStatusAction;
use Sharenjoy\NoahCms\Resources\Traits\NoahViewRecord;

class ViewEntry extends ViewRecord
{
    use NoahViewRecord;

    protected static string $resource = EntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CommentsAction::make()
                ->badgeColor('danger')
                ->label(__('noah-cms::noah-cms.comments'))
                ->badge(fn(Model $record) => $record->getAttribute('comments_count')),
            ActionGroup::make([
                UpdateSurveyEntryStatusAction::make(record: $this->record),
            ])
                ->label('更多操作')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('primary')
                ->button(),
        ];
    }
}
