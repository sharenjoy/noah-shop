<?php

namespace Sharenjoy\NoahShop\Resources\Survey;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Sharenjoy\NoahShop\Infolists\Components\SurveyAnswerEntry;
use Sharenjoy\NoahShop\Models\Survey\Answer;
use Sharenjoy\NoahShop\Resources\Survey\AnswerResource\Pages;
use Sharenjoy\NoahCms\Resources\Traits\NoahBaseResource;

class AnswerResource extends Resource implements HasShieldPermissions
{
    use NoahBaseResource;

    protected static ?string $model = Answer::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-arrow-uturn-left';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('noah-shop::noah-shop.survey.navigation.group');
    }

    public static function getLabel(): string
    {
        return __('noah-shop::noah-shop.survey.navigation.answer.label');
    }

    public static function getPluralLabel(): string
    {
        return __('noah-shop::noah-shop.survey.navigation.answer.plural-label');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['question', 'entry.participant', 'entry.survey']);
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
                Tables\Actions\ActionGroup::make(array_merge(static::getTableActions(), [])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make(array_merge(static::getBulkActions(), [])),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('noah-shop::noah-shop.survey.title.answer'))
                    ->schema([
                        SurveyAnswerEntry::make(''),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnswers::route('/'),
            //'create' => Pages\CreateAnswer::route('/create'),
            //'edit' => Pages\EditAnswer::route('/{record}/edit'),
            'view' => Pages\ViewAnswer::route('/{record}'),
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
