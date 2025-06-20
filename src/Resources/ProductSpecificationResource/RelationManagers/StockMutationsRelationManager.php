<?php

namespace Sharenjoy\NoahShop\Resources\ProductSpecificationResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahShop\Models\StockMutation as StockMutationModel;
use Sharenjoy\NoahShop\Resources\Traits\CanViewShop;

class StockMutationsRelationManager extends RelationManager
{
    use CanViewShop;

    protected static string $relationship = 'stockMutations';

    protected static ?string $icon = 'heroicon-o-archive-box-arrow-down';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('noah-cms::noah-cms.stock');
    }

    protected static function getRecordLabel(): ?string
    {
        return __('noah-cms::noah-cms.stock');
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->stockMutations->count();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(\Sharenjoy\NoahCms\Utils\Form::make(StockMutationModel::class, $form->getOperation(), ownerRecord: $this->getOwnerRecord()));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle(fn(StockMutationModel $record): string => "({$record->id}) {$record->no}")
            ->heading(__('noah-cms::noah-cms.stock'))
            ->searchable(false)
            ->columns(\Sharenjoy\NoahCms\Utils\Table::make(StockMutationModel::class))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(StockMutationModel::class))
            ->actions([
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
