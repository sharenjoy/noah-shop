<?php

namespace Sharenjoy\NoahShop\Resources\Survey\SurveyResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahShop\Models\Survey\Entry;
use Sharenjoy\NoahCms\Resources\Traits\NoahBaseRelationManager;

class EntriesRelationManager extends RelationManager
{
    use NoahBaseRelationManager;

    protected static string $relationship = 'entries';

    protected static ?string $icon = 'heroicon-o-folder-open';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('noah-shop::noah-shop.survey.navigation.entry.label');
    }

    protected static function getRecordLabel(): ?string
    {
        return __('noah-shop::noah-shop.survey.navigation.entry.label');
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->entries->count();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(\Sharenjoy\NoahCms\Utils\Form::make(Entry::class, $form->getOperation()));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle(fn(Entry $record): string => "({$record->id}) {$record->title}")
            ->heading(__('noah-shop::noah-shop.survey.navigation.entry.label'))
            ->columns(\Sharenjoy\NoahCms\Utils\Table::make(Entry::class))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(Entry::class))
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                // Tables\Actions\AttachAction::make()->preloadRecordSelect()->recordSelectSearchColumns(['title', 'description', 'slug'])->multiple(),
            ])
            ->actions([
                // Tables\Actions\DetachAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
