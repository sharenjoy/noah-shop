<?php

namespace Sharenjoy\NoahShop\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Sharenjoy\NoahShop\Models\ProductSpecification;
use Sharenjoy\NoahShop\Resources\ProductSpecificationResource\Pages;
use Sharenjoy\NoahShop\Resources\ProductSpecificationResource\RelationManagers\ProductRelationManager;
use Sharenjoy\NoahShop\Resources\ProductSpecificationResource\RelationManagers\StockMutationsRelationManager;
use Sharenjoy\NoahCms\Resources\Traits\NoahBaseResource;
use Tapp\FilamentValueRangeFilter\Filters\ValueRangeFilter;

class ProductSpecificationResource extends Resource implements HasShieldPermissions
{
    use NoahBaseResource;

    protected static ?string $model = ProductSpecification::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('noah-cms::noah-cms.product');
    }

    public static function getModelLabel(): string
    {
        return __('noah-cms::noah-cms.specification');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(\Sharenjoy\NoahCms\Utils\Form::make(static::getModel(), $form->getOperation()));
    }

    public static function table(Table $table): Table
    {
        $currentPanelId = \Filament\Facades\Filament::getCurrentPanel()->getId();
        $table = static::chainTableFunctions($table);
        return $table
            ->columns(array_merge(static::getTableStartColumns(), \Sharenjoy\NoahCms\Utils\Table::make(static::getModel())))
            ->filters(array_merge([
                ValueRangeFilter::make('price')
                    ->label(__('noah-cms::noah-cms.price'))
                    ->query(function ($query, $data) {
                        if (empty($data['range_condition'])) {
                            return;
                        }

                        // 根據 range_condition 判斷篩選條件
                        switch ($data['range_condition']) {
                            case 'between':
                                if (isset($data['range_between_from']) && isset($data['range_between_to'])) {
                                    $query->whereBetween('price', [
                                        (int) $data['range_between_from'],
                                        (int) $data['range_between_to'],
                                    ]);
                                }
                                break;

                            case 'greater_than':
                                if (isset($data['range_greater_than'])) {
                                    $query->where('price', '>', (int) $data['range_greater_than']);
                                }
                                break;

                            case 'greater_than_equal':
                                if (isset($data['range_greater_than_equal'])) {
                                    $query->where('price', '>=', (int) $data['range_greater_than_equal']);
                                }
                                break;

                            case 'less_than':
                                if (isset($data['range_less_than'])) {
                                    $query->where('price', '<', (int) $data['range_less_than']);
                                }
                                break;

                            case 'less_than_equal':
                                if (isset($data['range_less_than_equal'])) {
                                    $query->where('price', '<=', (int) $data['range_less_than_equal']);
                                }
                                break;

                            case 'equal':
                                if (isset($data['range_equal'])) {
                                    $query->where('price', '=', (int) $data['range_equal']);
                                }
                                break;

                            case 'not_equal':
                                if (isset($data['range_not_equal'])) {
                                    $query->where('price', '!=', (int) $data['range_not_equal']);
                                }
                                break;

                            default:
                                // 如果沒有匹配的條件，則不應用任何篩選
                                break;
                        }
                    }),
                ValueRangeFilter::make('stock')
                    ->label(__('noah-cms::noah-cms.stock'))
                    ->query(function ($query, $data) {
                        if (empty($data['range_condition'])) {
                            return;
                        }
                        $query->whereHas('stockMutations', function ($query) use ($data) {
                            $query->select(DB::raw('stockable_id, stockable_type, SUM(amount) as total'))
                                ->groupBy('stockable_id', 'stockable_type');

                            // 根據 range_condition 判斷篩選條件
                            switch ($data['range_condition']) {
                                case 'between':
                                    if (isset($data['range_between_from']) && isset($data['range_between_to'])) {
                                        $query->havingBetween('total', [
                                            (int) $data['range_between_from'],
                                            (int) $data['range_between_to'],
                                        ]);
                                    }
                                    break;

                                case 'greater_than':
                                    if (isset($data['range_greater_than'])) {
                                        $query->having('total', '>', (int) $data['range_greater_than']);
                                    }
                                    break;

                                case 'greater_than_equal':
                                    if (isset($data['range_greater_than_equal'])) {
                                        $query->having('total', '>=', (int) $data['range_greater_than_equal']);
                                    }
                                    break;

                                case 'less_than':
                                    if (isset($data['range_less_than'])) {
                                        $query->having('total', '<', (int) $data['range_less_than']);
                                    }
                                    break;

                                case 'less_than_equal':
                                    if (isset($data['range_less_than_equal'])) {
                                        $query->having('total', '<=', (int) $data['range_less_than_equal']);
                                    }
                                    break;

                                case 'equal':
                                    if (isset($data['range_equal'])) {
                                        $query->having('total', '=', (int) $data['range_equal']);
                                    }
                                    break;

                                case 'not_equal':
                                    if (isset($data['range_not_equal'])) {
                                        $query->having('total', '!=', (int) $data['range_not_equal']);
                                    }
                                    break;

                                default:
                                    // 如果沒有匹配的條件，則不應用任何篩選
                                    break;
                            }
                        });
                    }),
            ], \Sharenjoy\NoahCms\Utils\Filter::make(static::getModel())))
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make(array_merge(static::getTableActions(), [])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ])
            ->groups([
                Group::make('product_id')->label(__('noah-cms::noah-cms.product_title'))->collapsible(),
            ])
            ->reorderable(false);
    }

    public static function getRelations(): array
    {
        return [
            ProductRelationManager::class,
            StockMutationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductSpecifications::route('/'),
            // 'create' => Pages\CreateProductSpecification::route('/create'),
            'edit' => Pages\EditProductSpecification::route('/{record}/edit'),
            'view' => Pages\ViewProductSpecification::route('/{record}'),
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
