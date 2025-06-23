<?php

namespace Sharenjoy\NoahShop\Resources\Survey\EntryResource\Pages\Actions;

use Filament\Actions\Action;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Sharenjoy\NoahShop\Enums\SurveyEntryStatus;

class UpdateSurveyEntryStatusAction
{
    public static function make($record = null)
    {
        return Action::make('updateSurveyEntryStatusAction')
            ->label('更新問卷狀態')
            ->modalHeading('更新問卷狀態')
            ->color('primary')
            ->icon('heroicon-o-arrows-right-left')
            ->form([
                Section::make('問卷狀態')
                    ->extraAttributes(['style' => 'background-color: #f8f8f8'])
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                Select::make('status')
                                    ->label(__('noah-shop::noah-shop.status'))
                                    ->options(SurveyEntryStatus::visibleOptions())
                                    ->required(),

                                Textarea::make('content')->rows(2)->label(__('noah-shop::noah-shop.activity.label.status_notes')),
                            ]),
                    ]),
            ])
            ->mountUsing(function (ComponentContainer $form, $record) {
                $form->fill($record->toArray());
            })
            ->action(function (array $data, $record) {

                $statusEnum = SurveyEntryStatus::tryFrom($data['status']);

                if (! $statusEnum) {
                    Notification::make()
                        ->title('無效的問卷狀態')
                        ->danger()
                        ->send();

                    return;
                }

                $record->setStatus($data['status'], $data['content'] ?? null);

                Notification::make()
                    ->title('狀態更新完成')
                    ->success()
                    ->body('問卷狀態已更新為 ' . $statusEnum->getLabel())
                    ->send();
            })
            ->requiresConfirmation();
    }
}
