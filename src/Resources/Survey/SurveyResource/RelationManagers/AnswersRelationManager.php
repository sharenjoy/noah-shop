<?php

namespace Sharenjoy\NoahShop\Resources\Survey\SurveyResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahShop\Models\Survey\Answer;

class AnswersRelationManager extends RelationManager
{
    protected static string $relationship = 'answers';

    protected static ?string $icon = 'heroicon-o-ticket';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('noah-shop::noah-shop.user_coupon_status');
    }

    protected static function getRecordLabel(): ?string
    {
        return __('noah-shop::noah-shop.user_coupon_status');
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->answeres->count();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(\Sharenjoy\NoahCms\Utils\Form::make(Answer::class, $form->getOperation()));
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('noah-shop::noah-shop.user_coupon_status'))
            ->columns(\Sharenjoy\NoahCms\Utils\Table::make(Answer::class))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(Answer::class))
            ->searchable(false)
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                // Tables\Actions\AttachAction::make()->preloadRecordSelect()->recordSelectSearchColumns(['code'])->multiple(),
            ])
            ->actions([
                // Tables\Actions\DetachAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DetachBulkAction::make(),
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
