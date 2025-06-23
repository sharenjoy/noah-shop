<?php

namespace Sharenjoy\NoahShop\Models;

use Appstract\Stock\HasStock;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sharenjoy\NoahCms\Actions\Shop\ShopFeatured;
use Sharenjoy\NoahShop\Models\Product;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahCms\Models\Traits\HasMediaLibrary;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;

class ProductSpecification extends Model implements Sortable
{
    use CommonModelTrait;
    use HasFactory;
    use LogsActivity;
    use SortableTrait;
    use HasTranslations;
    use HasMediaLibrary;
    use HasStock;

    protected $casts = [
        'spec_detail_name' => 'json',
        'album' => 'array',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'spec',
    ];

    public $translatable = [
        'content',
    ];

    protected array $sort = [
        'order_column' => 'asc',
    ];

    protected function formFields(): array
    {
        return [
            'left' => [
                'spec_detail_name' => [],
                'no' => Section::make()->schema([
                    TextInput::make('no')->placeholder(__('noah-shop::noah-shop.spec_no'))->label(__('noah-shop::noah-shop.spec_no'))->unique(ProductSpecification::class, 'no', ignoreRecord: true),
                    TextInput::make('sku')->placeholder('SKU')->label('SKU')->unique(ProductSpecification::class, 'sku', ignoreRecord: true),
                    TextInput::make('barcode')->placeholder(__('noah-shop::noah-shop.spec_barcode'))->label(__('noah-shop::noah-shop.spec_barcode'))->unique(ProductSpecification::class, 'barcode', ignoreRecord: true),
                ])->columns(3),
                'price' => Section::make()->schema([
                    TextInput::make('price')->numeric()->placeholder(__('noah-shop::noah-shop.price'))->label(__('noah-shop::noah-shop.price'))->helperText('實際結帳價格'),
                    TextInput::make('compare_price')->numeric()->placeholder(__('noah-shop::noah-shop.compare_price'))->label(__('noah-shop::noah-shop.compare_price'))->helperText('通常可作為有刪除線的價格'),
                    TextInput::make('cost')->numeric()->placeholder(__('noah-shop::noah-shop.cost'))->label(__('noah-shop::noah-shop.cost')),
                ])->columns(3),
                'weight' => Section::make()->schema([
                    TextInput::make('weight')->numeric()->placeholder(1200)->label(__('noah-shop::noah-shop.weight'))->helperText('含包裝的重量，單位為公克(g)')
                ]),
                'content' => [
                    'profile' => 'simple',
                ],
            ],
            'right' => [
                'img' => ['required' => true],
                'album' => [],
                'is_active' => ['required' => true],
                'stock' => Section::make(__('noah-shop::noah-shop.stock'))
                    ->hidden(fn($record) => $record === null)
                    ->visible(fn(): bool => ShopFeatured::run('shop'))
                    ->schema([
                        Placeholder::make('stock')
                            ->label('')
                            ->content(fn($record): ?string => $record->stock),
                    ]),
            ],
        ];
    }

    protected function tableFields(): array
    {
        return [
            'thumbnail' => [],
            'product.title' =>  ['alias' => 'belongs_to', 'label' => 'product', 'relation' => 'product'],
            'spec_detail_name' => [],
            'no' => ['label' => 'spec_no'],
            'sku' => [],
            'stock' => TextColumn::make('stock')->label(__('noah-shop::noah-shop.stock'))->numeric()->toggleable()->visible(fn(): bool => ShopFeatured::run('shop')),
            'price' => ['type' => 'number'],
            'compare_price' => ['type' => 'number'],
            'weight' => ['type' => 'number'],
            'is_active' => [],
            'created_at' => ['isToggledHiddenByDefault' => true],
            'updated_at' => ['isToggledHiddenByDefault' => true],
        ];
    }

    /** RELACTIONS */

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** SCOPES */

    /** EVENTS */

    /** OTHERS */

    protected function spec(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => join(',', json_decode($attributes['spec_detail_name'], true))
        );
    }

    public function getLabelAttribute()
    {
        return "{$this->no} {$this->product->title} ({$this->spec})";
    }
}
