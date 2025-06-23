<?php

namespace Sharenjoy\NoahShop\Resources\ProductResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Actions\Table\RelationManagerAction;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahShop\Actions\StoreRecordBackToProductSpecs;
use Sharenjoy\NoahShop\Models\ProductSpecification;
use Sharenjoy\NoahShop\Resources\ProductSpecificationResource;
use Sharenjoy\NoahShop\Resources\ProductSpecificationResource\RelationManagers\StockMutationsRelationManager;
use Sharenjoy\NoahCms\Resources\Traits\NoahBaseRelationManager;

class ProductSpecificationsRelationManager extends RelationManager
{
    use NoahBaseRelationManager;

    protected static string $relationship = 'specifications';

    protected static ?string $icon = 'heroicon-o-square-3-stack-3d';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('noah-shop::noah-shop.specification');
    }

    protected static function getRecordLabel(): ?string
    {
        return __('noah-shop::noah-shop.specification');
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->specifications->count();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(\Sharenjoy\NoahCms\Utils\Form::make(ProductSpecification::class, $form->getOperation(), ownerRecord: $this->getOwnerRecord()));
    }

    public function table(Table $table): Table
    {
        $parentRecord = $this->getOwnerRecord();
        $headerActions = [];

        if (! $parentRecord['is_single_spec']) {
            $headerActions[] = Tables\Actions\CreateAction::make()
                ->mutateFormDataUsing(function (Tables\Actions\CreateAction $action, array $data): array {
                    try {
                        StoreRecordBackToProductSpecs::run($data['spec_detail_name'], $this->getOwnerRecord(), 'create');
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title(__('noah-shop::noah-shop.error'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();

                        $action->halt();
                    }

                    return $data;
                });
        }

        return $table
            ->recordTitle(fn(ProductSpecification $record): string => "({$record->id}) {$record->no}")
            ->heading(__('noah-shop::noah-shop.specification'))
            ->columns(array_merge(static::getTableStartColumns(ProductSpecificationResource::class), \Sharenjoy\NoahCms\Utils\Table::make(ProductSpecification::class)))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(ProductSpecification::class))
            ->headerActions($headerActions)
            ->actions([
                RelationManagerAction::make('product-specification-stock-mutations')
                    ->label('庫存狀態')
                    ->icon('heroicon-o-archive-box-arrow-down')
                    ->relationManager(StockMutationsRelationManager::make()),
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (Tables\Actions\EditAction $action, array $data, ProductSpecification $record): array {
                        try {
                            StoreRecordBackToProductSpecs::run($data['spec_detail_name'] ?? [], $this->getOwnerRecord(), 'edit', $record);
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title(__('noah-shop::noah-shop.error'))
                                ->body($e->getMessage())
                                ->danger()
                                ->send();

                            $action->halt();
                        }

                        return $data;
                    }),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('order_column')
            ->defaultSort('order_column')
            ->reorderRecordsTriggerAction(
                fn(Action $action, bool $isReordering) => $action
                    ->button()
                    ->label($isReordering ? __('noah-shop::noah-shop.reordering_completed') : __('noah-shop::noah-shop.start_reordering')),
            );
    }
}
