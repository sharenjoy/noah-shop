<?php

namespace Sharenjoy\NoahShop\Models;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sharenjoy\NoahCms\Actions\GenerateSeriesNumber;
use Sharenjoy\NoahShop\Actions\Shop\ChargeExpireDateSetting;
use Sharenjoy\NoahShop\Enums\PaymentMethod;
use Sharenjoy\NoahShop\Enums\PaymentProvider;
use Sharenjoy\NoahShop\Enums\TransactionStatus;
use Sharenjoy\NoahShop\Models\Invoice;
use Sharenjoy\NoahShop\Models\Order;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    use CommonModelTrait;
    use LogsActivity;

    protected $casts = [
        'status' => TransactionStatus::class,
        'provider' => PaymentProvider::class,
        'payment_method' => PaymentMethod::class,
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    protected array $sort = [
        'created_at' => 'desc',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->sn) {
                $model->sn = GenerateSeriesNumber::run(model: 'transaction', prefix: 'T', strLeng: 4);
            }

            if ($model->payment_method == PaymentMethod::ATM->value) {
                $model = ChargeExpireDateSetting::run(transaction: $model);
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
            'provider' => TextColumn::make('provider')
                ->label(__('noah-cms::noah-cms.activity.label.provider'))
                ->sortable()
                ->searchable()
                ->badge(PaymentProvider::class),
            'payment_method' => TextColumn::make('payment_method')
                ->label(__('noah-cms::noah-cms.activity.label.payment_method'))
                ->sortable()
                ->searchable()
                ->badge(PaymentMethod::class),
            'status' => ['label' => 'activity.label.status', 'model' => 'Payment'],
            'order_transaction' => ['alias' => 'OrderTransaction', 'label' => 'order_transaction'],
            'dates' => [],
        ];;
    }

    /** RELACTIONS */

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /** SCOPES */

    /** EVENTS */

    /** OTHERS */
}
