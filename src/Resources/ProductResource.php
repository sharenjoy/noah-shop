<?php

namespace Sharenjoy\NoahShop\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Sharenjoy\NoahShop\Models\Brand;
use Sharenjoy\NoahShop\Models\Product;
use Sharenjoy\NoahShop\Resources\ProductResource\Pages;
use Sharenjoy\NoahShop\Resources\ProductResource\RelationManagers\ProductSpecificationsRelationManager;
use Sharenjoy\NoahCms\Resources\Traits\NoahBaseResource;

class ProductResource extends Resource implements HasShieldPermissions
{
    use NoahBaseResource;

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('noah-cms::noah-cms.product');
    }

    public static function getModelLabel(): string
    {
        return __('noah-cms::noah-cms.product');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['brand', 'specifications', 'tags', 'categories']);
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
            ->filters(array_merge([
                Filter::make('brands')
                    ->form([
                        Select::make('brands')
                            ->label(__('noah-cms::noah-cms.brand'))
                            ->options(Brand::all()->pluck('title', 'id'))
                            ->prefixIcon('heroicon-o-lifebuoy')
                            ->multiple()
                            ->searchable(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $query->when($data['brands'], function ($query, $brands) {
                            return $query->whereHas('brand', fn($query) => $query->whereIn('brands.id', $brands));
                        });
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['brands'] ?? null) {
                            return __('noah-cms::noah-cms.brand') . ': ' . implode(', ', Brand::whereIn('id', $data['brands'])->get()->pluck('title')->toArray());
                        }

                        return null;
                    }),
            ], \Sharenjoy\NoahCms\Utils\Filter::make(static::getModel())))
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make(array_merge(static::getTableActions(), [])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make(array_merge(static::getBulkActions(), [])),
            ])
            ->groups([
                Group::make('brand_id')->label(__('noah-cms::noah-cms.brand'))->getTitleFromRecordUsing(fn($record): string => $record->brand->title)->collapsible(),
            ])
            ->reorderable(false);
    }

    public static function getRelations(): array
    {
        return [
            ProductSpecificationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return array_merge(static::getCommonPermissionPrefixes(), []);
    }
}
