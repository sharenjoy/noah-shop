<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahCms\Models\InvoicePrice;

class InvoicePricesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoicePrices';

    protected static ?string $icon = 'heroicon-o-currency-dollar';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('noah-cms::noah-cms.price_items');
    }

    protected static function getRecordLabel(): ?string
    {
        return __('noah-cms::noah-cms.price_items');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['invoice', 'order']);
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(\Sharenjoy\NoahCms\Utils\Form::make(InvoicePrice::class, $form->getOperation()));
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('noah-cms::noah-cms.price_items'))
            ->searchable(false)
            ->columns(\Sharenjoy\NoahCms\Utils\Table::make(InvoicePrice::class))
            // ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(InvoicePrice::class, Role::class))
            ->headerActions([
                // Tables\Actions\AttachAction::make()->preloadRecordSelect()->recordSelectSearchColumns(['name', 'email'])->multiple(),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
