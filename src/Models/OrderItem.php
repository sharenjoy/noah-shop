<?php

namespace Sharenjoy\NoahShop\Models;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sharenjoy\NoahShop\Enums\OrderItemType;
use Sharenjoy\NoahShop\Models\Product;
use Sharenjoy\NoahShop\Models\ProductSpecification;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahShop\Models\Traits\HasPromos;
use Spatie\Activitylog\Traits\LogsActivity;

class OrderItem extends Model
{
    use CommonModelTrait;
    use LogsActivity;
    use HasPromos;

    protected $casts = [
        'type' => OrderItemType::class,
        'preorder' => 'boolean',
        'quantity' => 'integer',
        'product_details' => 'json',
    ];

    protected $appends = [
        'price_discounted',
        'subtotal',
    ];

    protected array $sort = [
        'created_at' => 'asc',
    ];

    protected function formFields(): array
    {
        return [];
    }

    protected function tableFields(): array
    {
        return [
            'spec_img' => ['alias' => 'image'],
            'product.title' => ['alias' => 'belongs_to', 'label' => 'product_title', 'relation' => 'product'],
            'productSpecification.spec_detail_name' => ['alias' => 'belongs_to', 'label' => 'spec_detail_name', 'relation' => 'productSpecification'],
            'type' => TextColumn::make('type')
                ->label(__('noah-cms::noah-cms.type'))
                ->sortable()
                ->searchable()
                ->badge(OrderItemType::class),
            'price' => ['type' => 'number', 'summarize' => ['sum']],
            'discount' => ['type' => 'number', 'summarize' => ['sum']],
            'currency' => [],
            'quantity' => ['type' => 'number'],
            'order_item_subtotal' => TextColumn::make('id')
                ->label(__('noah-cms::noah-cms.subtotal'))
                ->sortable()
                ->formatStateUsing(function ($state, $record) {
                    return currency_format($record->subtotal, $record->currency);
                }),
            'weight' => TextColumn::make('id')
                ->label(__('noah-cms::noah-cms.order_item_weight'))
                ->sortable()
                ->formatStateUsing(function ($state, $record) {
                    return number_format($record->productSpecification->weight * $record->quantity) . '(g)';
                }),
            'created_at' => ['isToggledHiddenByDefault' => true],
            'updated_at' => ['isToggledHiddenByDefault' => true],
        ];
    }

    /** RELACTIONS */

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function productSpecification(): BelongsTo
    {
        return $this->belongsTo(ProductSpecification::class);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(OrderShipment::class);
    }

    /** SCOPES */

    /** EVENTS */

    /** OTHERS */

    protected function priceDiscounted(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['price'] + $attributes['discount']
        );
    }

    protected function subtotal(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => ($attributes['price'] + $attributes['discount']) * $attributes['quantity']
        );
    }
}
