<?php

namespace Sharenjoy\NoahShop\Resources\Survey;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Sharenjoy\NoahShop\Enums\SurveyEntryStatus;
use Sharenjoy\NoahShop\Infolists\Components\SurveyEntry;
use Sharenjoy\NoahShop\Models\Survey\Entry;
use Sharenjoy\NoahShop\Resources\Survey\EntryResource\Pages;
use Sharenjoy\NoahShop\Resources\Survey\EntryResource\RelationManagers\StatusesRelationManager;
use Sharenjoy\NoahCms\Resources\Traits\NoahBaseResource;

class EntryResource extends Resource implements HasShieldPermissions
{
    use NoahBaseResource;

    protected static ?string $model = Entry::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-bars-4';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('noah-shop::noah-shop.survey.navigation.group');
    }

    public static function getLabel(): string
    {
        return __('noah-shop::noah-shop.survey.navigation.entry.label');
    }

    public static function getPluralLabel(): string
    {
        return __('noah-shop::noah-shop.survey.navigation.entry.plural-label');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['answers.question', 'survey', 'participant']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $table = static::chainTableFunctions($table);
        return $table
            ->columns(array_merge(static::getTableStartColumns(), \Sharenjoy\NoahCms\Utils\Table::make(static::getModel())))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(static::getModel()))
            ->actions([
                Tables\Actions\ActionGroup::make(array_merge([
                    Action::make('update_status')
                        ->label('更新狀態')
                        ->icon('heroicon-o-arrows-right-left')
                        ->form([
                            Select::make('status')
                                ->label(__('noah-shop::noah-shop.status'))
                                ->options(SurveyEntryStatus::visibleOptions())
                                ->required(),
                            Textarea::make('content')->rows(2)->label(__('noah-shop::noah-shop.activity.label.status_notes')),
                        ])
                        ->mountUsing(function (ComponentContainer $form, $record) {
                            $form->fill($record->toArray());
                        })
                        ->action(function (Entry $record, array $data) {
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
                        }),
                ], static::getTableActions())),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make(array_merge([
                    BulkAction::make('updateSurveyEntriesStatus')
                        ->label('更新狀態')
                        ->icon('heroicon-o-arrows-right-left')
                        ->form([
                            Select::make('status')
                                ->label(__('noah-shop::noah-shop.status'))
                                ->options(SurveyEntryStatus::visibleOptions())
                                ->required(),
                            Textarea::make('content')->rows(2)->label(__('noah-shop::noah-shop.activity.label.status_notes')),
                        ])
                        ->action(function ($records, array $data) {
                            $statusEnum = SurveyEntryStatus::tryFrom($data['status']);

                            if (! $statusEnum) {
                                Notification::make()
                                    ->title('無效的問卷狀態')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            foreach ($records as $record) {
                                $record->setStatus($data['status'], $data['content'] ?? null);
                            }

                            Notification::make()
                                ->title('狀態更新完成')
                                ->success()
                                ->body('問卷狀態已更新為 ' . $statusEnum->getLabel())
                                ->send();
                        }),
                ], static::getBulkActions())),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('noah-shop::noah-shop.survey.title.survey'))
                    ->schema([
                        SurveyEntry::make(''),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            StatusesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntries::route('/'),
            //'create' => Pages\CreateEntry::route('/create'),
            //'edit' => Pages\EditEntry::route('/{record}/edit'),
            'view' => Pages\ViewEntry::route('/{record}'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'delete',
            'delete_any',
            'restore',
            'restore_any',
            'force_delete',
            'force_delete_any',
        ];
    }
}
