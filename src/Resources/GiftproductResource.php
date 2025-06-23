<?php

namespace Sharenjoy\NoahShop\Resources;

use Awcodes\Shout\Components\Shout;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Sharenjoy\NoahShop\Models\Giftproduct;
use Sharenjoy\NoahShop\Models\ProductSpecification;
use Sharenjoy\NoahShop\Resources\GiftproductResource\Pages;
use Sharenjoy\NoahCms\Resources\Traits\NoahBaseResource;

class GiftproductResource extends Resource implements HasShieldPermissions
{
    use NoahBaseResource;

    protected static ?string $model = Giftproduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift-top';

    protected static ?int $navigationSort = 8;

    public static function getNavigationGroup(): ?string
    {
        return __('noah-shop::noah-shop.product');
    }

    public static function getModelLabel(): string
    {
        return __('noah-shop::noah-shop.giftproduct');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema(array_merge([
                Section::make('')->schema([
                    Select::make('product_specification_id')
                        ->label(__('noah-shop::noah-shop.specification'))
                        ->options(function () {
                            return ProductSpecification::all()->pluck('label', 'id');
                        })
                        ->searchable(['no', 'spec_detail_name', 'sku', 'barcode'])
                        ->preload()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, Set $set) {
                            $productSpec = ProductSpecification::find($state);
                            $product = $productSpec->product;
                            $set('title', $product->getTranslations('title'));
                            $set('description', $product->getTranslations('description'));
                            $set('content', $productSpec->getTranslations('content')['zh_TW'] ?? $product->getTranslations('content'));
                            $set('slug', $product->slug . '-' . $productSpec->no);
                            $set('img', $productSpec->img ?? $product->img);
                            $set('album', $productSpec->album ?? $product->album);
                        })
                        ->distinct(),
                ]),
                Shout::make('')
                    ->content(new HtmlString('請選擇對應的商品規格以後，下方的贈品內容會自動抓取商品規格與商品的內容。<br>當然您也可以自訂贈品的內容！'))
                    ->type('info')->color(Color::Indigo)->columnSpanFull(),
            ], \Sharenjoy\NoahCms\Utils\Form::make(static::getModel(), $form->getOperation())));
    }

    public static function table(Table $table): Table
    {
        $table = static::chainTableFunctions($table);
        return $table
            ->columns(array_merge(static::getTableStartColumns(), \Sharenjoy\NoahCms\Utils\Table::make(static::getModel())))
            ->filters(\Sharenjoy\NoahCms\Utils\Filter::make(static::getModel()))
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make(array_merge(static::getTableActions(), [])),
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
            'index' => Pages\ListGiftproducts::route('/'),
            'create' => Pages\CreateGiftproduct::route('/create'),
            'edit' => Pages\EditGiftproduct::route('/{record}/edit'),
            'view' => Pages\ViewGiftproduct::route('/{record}'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return array_merge(static::getCommonPermissionPrefixes(), []);
    }
}
