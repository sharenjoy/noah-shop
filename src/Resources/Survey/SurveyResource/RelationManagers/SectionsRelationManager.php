<?php

namespace Sharenjoy\NoahShop\Resources\Survey\SurveyResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahShop\Models\Survey\Section;
use Sharenjoy\NoahCms\Resources\Traits\NoahBaseRelationManager;

class SectionsRelationManager extends RelationManager
{
    use NoahBaseRelationManager;

    protected static string $relationship = 'sections';

    protected static ?string $icon = 'heroicon-o-folder-open';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('noah-shop::noah-shop.survey.navigation.section.label');
    }

    protected static function getRecordLabel(): ?string
    {
        return __('noah-shop::noah-shop.survey.navigation.section.label');
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->sections->count();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(\Sharenjoy\NoahCms\Utils\Form::make(Section::class, $form->getOperation()));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle(fn(Section $record): string => "({$record->id}) {$record->title}")
            ->heading(__('noah-shop::noah-shop.survey.navigation.section.label'))
            ->columns(\Sharenjoy\NoahCms\Utils\Table::make(Section::class))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(Section::class))
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                // Tables\Actions\AttachAction::make()->preloadRecordSelect()->recordSelectSearchColumns(['title', 'description', 'slug'])->multiple(),
            ])
            ->actions([
                // Tables\Actions\DetachAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('order_column')
            ->defaultSort('order_column');
    }
}
