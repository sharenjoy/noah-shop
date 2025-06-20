<?php

namespace Sharenjoy\NoahShop\Models;

use Coolsam\NestedComments\Concerns\HasComments;
use Coolsam\NestedComments\Concerns\HasReactions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Sharenjoy\NoahCms\Actions\GenerateSeriesNumber;
use Sharenjoy\NoahShop\Enums\OrderShipmentStatus;
use Sharenjoy\NoahShop\Enums\OrderStatus;
use Sharenjoy\NoahShop\Enums\TransactionStatus;
use Sharenjoy\NoahShop\Models\Invoice;
use Sharenjoy\NoahShop\Models\InvoicePrice;
use Sharenjoy\NoahShop\Models\OrderItem;
use Sharenjoy\NoahShop\Models\OrderShipment;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahShop\Models\Transaction;
use Sharenjoy\NoahShop\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;

class BaseOrder extends Model
{
    use CommonModelTrait;
    use HasFactory;
    use LogsActivity;
    use HasComments;
    // use HasReactions;

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    protected array $sort = [
        'created_at' => 'desc',
        'id' => 'desc',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->sn) {
                $model->sn = GenerateSeriesNumber::run('order');
            }
        });
    }

    protected function formFields(): array
    {
        return [];
    }

    protected function tableFields(): array
    {
        return [
            'notes' => \Filament\Tables\Columns\IconColumn::make('notes')
                ->label(__('noah-cms::noah-cms.order_notes'))
                ->tooltip(fn($state) => $state)
                ->width('1%')
                ->alignCenter()
                ->placeholder('-')
                ->sortable()
                ->icon('heroicon-o-document-text')
                ->size(\Filament\Tables\Columns\IconColumn\IconColumnSize::Medium),
            'sn' => ['alias' => 'order_sn', 'label' => 'order_sn'],
            'status' => ['label' => 'order_status', 'model' => 'order'],
            'order_items' => \Filament\Tables\Columns\TextColumn::make('items_count')
                ->counts('items')
                ->badge()
                ->color('gray')
                ->label(__('noah-cms::noah-cms.order_item_counts'))
                ->tooltip('點擊可快速查看品項')
                ->sortable()
                ->action($this->viewOrderItemsAction()),
            'order_user' => [],
            'order_shipment' => [],
            'order_transaction' => [],
            'order_invoice' => [],
            'dates' => [],
        ];
    }

    /** RELACTIONS */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(OrderShipment::class, 'order_id')->orderBy('created_at', 'desc');
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(OrderShipment::class, 'order_id')->orderBy('created_at', 'desc');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'order_id');
    }

    public function invoicePrices(): HasMany
    {
        return $this->hasMany(InvoicePrice::class, 'order_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'order_id')->orderBy('created_at', 'desc');
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'order_id')->orderBy('created_at', 'desc');
    }

    /** SCOPES */

    // 非取消已成立的訂單
    public function scopeValidOrders($query): Builder
    {
        return $query->whereNotIn('status', [
            OrderStatus::Initial,
            OrderStatus::Cancelled
        ]);
    }

    // 已成立的訂單
    public function scopeEstablishedOrders($query): Builder
    {
        return $query->whereNotIn('status', [
            OrderStatus::Initial,
        ]);
    }

    public function scopeNew($query): Builder
    {
        return $query->whereIn('status', [
            OrderStatus::New,
        ]);
    }

    public function scopePending($query): Builder
    {
        return $query->whereIn('status', [
            OrderStatus::Pending,
        ]);
    }

    public function scopeCancelled($query): Builder
    {
        return $query->whereIn('status', [
            OrderStatus::Cancelled,
        ]);
    }

    public function scopeFinished($query): Builder
    {
        return $query->whereIn('status', [
            OrderStatus::Finished,
        ]);
    }

    // 可出貨訂單
    public function scopeShippable($query): Builder
    {
        return $query->whereIn('status', [
            OrderStatus::Processing,
        ])->whereHas('shipment', function ($query) {
            $query->whereIn('status', [OrderShipmentStatus::New]);
        })->whereHas('transaction', function ($query) {
            $query->where('status', TransactionStatus::Paid)->orWhere('total_price', 0);
        });
    }

    // 已出貨訂單
    public function scopeShipped($query): Builder
    {
        return $query->whereIn('status', [
            OrderStatus::Processing,
        ])->whereHas('shipment', function ($query) {
            $query->whereIn('status', [OrderShipmentStatus::Shipped]);
        });
    }

    // 已送達訂單
    public function scopeDelivered($query): Builder
    {
        return $query->whereIn('status', [
            OrderStatus::Processing,
        ])->whereHas('shipment', function ($query) {
            $query->whereIn('status', [OrderShipmentStatus::Delivered]);
        });
    }

    // 退貨/退款/取消中訂單
    public function scopeIssued($query): Builder
    {
        return $query->where('status', OrderStatus::Processing)
            ->where(function ($query) {
                $query->whereHas('shipment', function ($query) {
                    $query->whereIn('status', [
                        OrderShipmentStatus::Returning,
                        OrderShipmentStatus::Returned,
                    ]);
                })->orWhereHas('transaction', function ($query) {
                    $query->whereIn('status', [
                        TransactionStatus::Refunding,
                        TransactionStatus::Refunded,
                    ]);
                });
            });
    }

    public function getCurrentScope(): ?string
    {
        if ($this->status === OrderStatus::New) {
            return 'new';
        }

        if (
            $this->status === OrderStatus::Processing &&
            $this->shipment &&
            in_array($this->shipment->status, [OrderShipmentStatus::New]) &&
            ($this->transaction && ($this->transaction->status === TransactionStatus::Paid || $this->transaction->total_price == 0))
        ) {
            return 'shippable';
        }

        if (
            $this->status === OrderStatus::Processing &&
            $this->shipment &&
            in_array($this->shipment->status, [OrderShipmentStatus::Shipped])
        ) {
            return 'shipped';
        }

        if (
            $this->status === OrderStatus::Processing &&
            $this->shipment &&
            in_array($this->shipment->status, [OrderShipmentStatus::Delivered])
        ) {
            return 'delivered';
        }

        if (
            $this->status === OrderStatus::Processing &&
            (
                ($this->shipment && in_array($this->shipment->status, [
                    OrderShipmentStatus::Returning,
                    OrderShipmentStatus::Returned,
                ])) ||
                ($this->transaction && in_array($this->transaction->status, [
                    TransactionStatus::Refunding,
                    TransactionStatus::Refunded,
                ]))
            )
        ) {
            return 'issued';
        }

        if ($this->status === OrderStatus::Pending) {
            return 'pending';
        }

        if ($this->status === OrderStatus::Cancelled) {
            return 'cancelled';
        }

        if ($this->status === OrderStatus::Finished) {
            return 'finished';
        }

        return null;
    }

    /** EVENTS */

    /** OTHERS */

    protected static function newFactory()
    {
        return \Sharenjoy\NoahShop\Database\Factories\OrderFactory::new();
    }

    /**
     * Spatie Activity Log 會自動呼叫 model 的 getMorphClass() 來決定 subject_type
     * 你如果覆寫這個 method，就能指定寫進 log 的是什麼類別
     * 所以不管外面操作的是 NewOrder、IssuedOrder，
     * log 記錄時都統一成 \Sharenjoy\NoahShop\Models\Order
     * @return string
     */
    public function getMorphClass()
    {
        return \Sharenjoy\NoahShop\Models\Order::class; // 你想要寫入 activity_log 裡的 class 名稱
    }
}
