<?php

namespace Sharenjoy\NoahShop\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Sharenjoy\NoahShop\Models\Currency;
use Sharenjoy\NoahCms\Resources\CategoryResource\RelationManagers\ProductsRelationManager;
use Sharenjoy\NoahShop\Resources\CurrencyResource\Pages;
use Sharenjoy\NoahCms\Resources\Traits\NoahBaseResource;

class CurrencyResource extends Resource implements HasShieldPermissions
{
    use NoahBaseResource;

    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): ?string
    {
        return __('noah-cms::noah-cms.product');
    }

    public static function getModelLabel(): string
    {
        return __('noah-cms::noah-cms.currency');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(\Sharenjoy\NoahCms\Utils\Form::make(static::getModel(), $form->getOperation()));
    }

    public static function table(Table $table): Table
    {
        $table = static::chainTableFunctions($table);
        return $table
            ->columns(array_merge(static::getTableStartColumns(), \Sharenjoy\NoahCms\Utils\Table::make(static::getModel())))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(static::getModel()))
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\ActionGroup::make(array_merge(static::getTableActions(), [])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make(array_merge(static::getBulkActions(), [])),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrencies::route('/'),
            // 'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
            'view' => Pages\ViewCurrency::route('/{record}'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'update',
        ];
    }
}
