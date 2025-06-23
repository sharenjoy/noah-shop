<?php

namespace Sharenjoy\NoahShop\Models;

use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sharenjoy\NoahCms\Actions\GenerateSeriesNumber;
use Sharenjoy\NoahShop\Enums\DeliveryProvider;
use Sharenjoy\NoahShop\Enums\DeliveryType;
use Sharenjoy\NoahShop\Enums\OrderShipmentStatus;
use Sharenjoy\NoahShop\Models\Order;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class OrderShipment extends Model
{
    use CommonModelTrait;
    use LogsActivity;

    protected $casts = [
        'status' => OrderShipmentStatus::class,
        'provider' => DeliveryProvider::class,
        'delivery_type' => DeliveryType::class,
    ];

    protected $appends = [
        'delivery_method',
        'call',
    ];

    protected array $sort = [
        'created_at' => 'desc',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->sn) {
                $model->sn = GenerateSeriesNumber::run(model: 'shipment', prefix: 'S', strLeng: 4);
            }
        });
    }

    protected function formFields(): array
    {
        return [
            'left' => [
                'provider' => Select::make('provider')
                    ->label(__('noah-shop::noah-shop.delivery_provider'))
                    ->options(DeliveryProvider::class),
            ],
            'right' => [],
        ];
    }

    protected function tableFields(): array
    {
        return [
            'provider' => TextColumn::make('provider')
                ->label(__('noah-shop::noah-shop.activity.label.provider'))
                ->sortable()
                ->searchable()
                ->badge(DeliveryProvider::class),
            'delivery_type' => TextColumn::make('delivery_type')
                ->label(__('noah-shop::noah-shop.activity.label.delivery_type'))
                ->sortable()
                ->searchable()
                ->badge(DeliveryType::class),
            'status' => ['label' => 'order_shipment_status', 'model' => 'OrderShipment'],
            'shipment' => ['alias' => 'OrderShipment', 'label' => 'order_shipment'],
            'dates' => [],
        ];
    }

    /** RELACTIONS */

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** SCOPES */

    /** EVENTS */

    /** OTHERS */

    protected function deliveryMethod(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => DeliveryProvider::getLabelFromOption($attributes['provider']) . ' ' . DeliveryType::getLabelFromOption($attributes['delivery_type']),
        );
    }

    protected function call(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => '+' . $attributes['calling_code'] . ' ' . $attributes['mobile']
        );
    }
}
