<?php

namespace Sharenjoy\NoahShop\Resources\Survey;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;
use Sharenjoy\NoahShop\Jobs\SendExportSurveys;
use Sharenjoy\NoahShop\Models\Survey\Survey;
use Sharenjoy\NoahShop\Resources\Survey\SurveyResource\Pages;
use Sharenjoy\NoahShop\Resources\Survey\SurveyResource\RelationManagers\EntriesRelationManager;
use Sharenjoy\NoahShop\Resources\Survey\SurveyResource\RelationManagers\QuestionsRelationManager;
use Sharenjoy\NoahShop\Resources\Survey\SurveyResource\RelationManagers\SectionsRelationManager;
use Sharenjoy\NoahCms\Resources\Traits\NoahBaseResource;

class SurveyResource extends Resource implements HasShieldPermissions
{
    use NoahBaseResource;

    protected static ?string $model = Survey::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('noah-shop::noah-shop.survey.navigation.group');
    }

    public static function getModelLabel(): string
    {
        return __('noah-shop::noah-shop.survey.navigation.survey.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(array_merge(
                \Sharenjoy\NoahCms\Utils\Form::make(static::getModel(), $form->getOperation()),
                [
                    Section::make('問卷規則設定')
                        ->schema([
                            Radio::make('allow_guest')
                                ->label(__('noah-shop::noah-shop.survey.title.allow_guest'))
                                ->helperText(new HtmlString(__('noah-shop::noah-shop.survey.help.allow_guest')))
                                ->options([
                                    true => __('noah-shop::noah-shop.yes'),
                                    false => __('noah-shop::noah-shop.no'),
                                ])
                                ->default(false)
                                ->inline()
                                ->inlineLabel(false)
                                ->live(),

                            Fieldset::make('duration')
                                ->columns(1)
                                ->label(__('noah-shop::noah-shop.survey.title.duration_set'))
                                ->schema([
                                    Radio::make('forever')
                                        ->label(__('noah-shop::noah-shop.survey.title.forever'))
                                        ->options([
                                            true => __('noah-shop::noah-shop.yes'),
                                            false => __('noah-shop::noah-shop.no'),
                                        ])
                                        ->default(true)
                                        ->inline()
                                        ->inlineLabel(false)
                                        ->live(),
                                    DateTimePicker::make('started_at')
                                        ->displayFormat('Y-m-d H:i:s') // 顯示格式
                                        ->label(__('noah-shop::noah-shop.survey.title.started_at'))
                                        ->placeholder('2020-03-18 09:48:00')
                                        ->prefixIcon('heroicon-o-clock')
                                        ->format('Y-m-d H:i:s')
                                        ->required()
                                        ->native(false)
                                        ->live()
                                        ->visible(fn(Get $get): bool => !$get('forever')),
                                    DateTimePicker::make('expired_at')
                                        ->displayFormat('Y-m-d H:i:s') // 顯示格式
                                        ->label(__('noah-shop::noah-shop.survey.title.expired_at'))
                                        ->placeholder('2020-03-18 09:48:00')
                                        ->prefixIcon('heroicon-o-clock')
                                        ->format('Y-m-d H:i:s')
                                        ->required()
                                        ->rules(['required', 'date', 'after_or_equal:started_at'])
                                        ->native(false)
                                        ->minDate(fn(Get $get) => $get('started_at'))
                                        ->visible(fn(Get $get): bool => !$get('forever')),
                                ]),

                            Fieldset::make('limit_set')
                                ->columns(1)
                                ->label(__('noah-shop::noah-shop.survey.title.limit_set'))
                                ->schema([
                                    Radio::make('limit')
                                        ->label(__('noah-shop::noah-shop.survey.title.limit'))
                                        ->helperText(new HtmlString(__('noah-shop::noah-shop.survey.help.limit')))
                                        ->options([
                                            true => __('noah-shop::noah-shop.yes'),
                                            false => __('noah-shop::noah-shop.no'),
                                        ])
                                        ->default(false)
                                        ->inline()
                                        ->inlineLabel(false)
                                        ->live(),
                                    TextInput::make('limit_amount')
                                        ->label(__('noah-shop::noah-shop.survey.title.limit_amount'))
                                        ->helperText(new HtmlString(__('noah-shop::noah-shop.survey.help.limit_amount')))
                                        ->visible(function (Get $get) {
                                            return $get('limit');
                                        })
                                        ->prefixIcon('heroicon-o-arrow-trending-up')
                                        ->suffix('次')
                                        ->placeholder(1000)
                                        ->minValue(0)
                                        ->numeric(),
                                    TextInput::make('limit_per_participant')
                                        ->label(__('noah-shop::noah-shop.survey.title.limit_per_participant'))
                                        ->helperText(new HtmlString(__('noah-shop::noah-shop.survey.help.limit_per_participant')))
                                        ->visible(function (Get $get) {
                                            return $get('limit');
                                        })
                                        ->prefixIcon('heroicon-o-arrow-trending-up')
                                        ->suffix('次')
                                        ->placeholder(1)
                                        ->minValue(0)
                                        ->numeric(),
                                ]),
                            Fieldset::make('purchase_set')
                                ->columns(1)
                                ->label(__('noah-shop::noah-shop.survey.title.purchase_set'))
                                ->schema([
                                    Radio::make('purchaseable')
                                        ->label(__('noah-shop::noah-shop.survey.title.purchaseable'))
                                        ->helperText(new HtmlString(__('noah-shop::noah-shop.survey.help.purchaseable')))
                                        ->options([
                                            true => __('noah-shop::noah-shop.yes'),
                                            false => __('noah-shop::noah-shop.no'),
                                        ])
                                        ->default(false)
                                        ->inline()
                                        ->inlineLabel(false)
                                        ->live(),
                                    Radio::make('purchase_depends_on_option')
                                        ->label(__('noah-shop::noah-shop.survey.title.purchase_depends_on_option'))
                                        ->helperText(new HtmlString(__('noah-shop::noah-shop.survey.help.purchase_depends_on_option')))
                                        ->options([
                                            true => __('noah-shop::noah-shop.yes'),
                                            false => __('noah-shop::noah-shop.no'),
                                        ])
                                        ->default(true)
                                        ->visible(function (Get $get) {
                                            return $get('purchaseable');
                                        })
                                        ->inline()
                                        ->inlineLabel(false)
                                        ->live(),
                                    TextInput::make('purchase_price')
                                        ->label(__('noah-shop::noah-shop.survey.title.purchase_price'))
                                        ->helperText(new HtmlString(__('noah-shop::noah-shop.survey.help.purchase_price')))
                                        ->required()
                                        ->placeholder('100')
                                        ->rules(['required', 'numeric'])
                                        ->visible(function (Get $get) {
                                            return $get('purchaseable') && !$get('purchase_depends_on_option');
                                        })
                                        ->prefixIcon('heroicon-o-currency-dollar')
                                        ->suffix('元')
                                        ->minValue(0)
                                        ->numeric(),
                                ]),
                        ]),
                ],
            ));
    }

    public static function table(Table $table): Table
    {
        $table = static::chainTableFunctions($table);
        return $table
            ->columns(array_merge(static::getTableStartColumns(), \Sharenjoy\NoahCms\Utils\Table::make(static::getModel())))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(static::getModel()))
            ->actions([
                Action::make(__('noah-shop::noah-shop.survey.title.export_answers'))
                    ->icon('heroicon-s-arrow-down-tray')
                    ->action(function (Survey $record) {
                        SendExportSurveys::dispatch(user: request()->user(), survey: $record);

                        Notification::make()
                            ->title(__('You will receive your export via email'))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make(array_merge(static::getTableActions(), [])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make(array_merge(static::getBulkActions(), [
                    BulkAction::make(__('noah-shop::noah-shop.survey.title.export_answers'))
                        ->icon('heroicon-s-arrow-down-tray')
                        ->action(function (Collection $records) {
                            SendExportSurveys::dispatch(user: request()->user(), surveys: $records);

                            Notification::make()
                                ->title(__('You will receive your export via email'))
                                ->success()
                                ->send();
                        }),
                ])),
            ]);
    }

    public function export(Survey $survey)
    {
        SendExportSurveys::dispatch(user: request()->user(), survey: $survey);

        Notification::make()
            ->title(__('You will receive your export via email'))
            ->success()
            ->send();
    }

    public function exportBulk(Collection $surveys)
    {
        SendExportSurveys::dispatch(user: request()->user(), surveys: $surveys);

        Notification::make()
            ->title(__('You will receive your export via email'))
            ->success()
            ->send();
    }

    public static function getRelations(): array
    {
        return [
            SectionsRelationManager::class,
            QuestionsRelationManager::class,
            EntriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSurveys::route('/'),
            'create' => Pages\CreateSurvey::route('/create'),
            'edit' => Pages\EditSurvey::route('/{record}/edit'),
            'view' => Pages\ViewSurvey::route('/{record}'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return array_merge(static::getCommonPermissionPrefixes(), []);
    }
}
