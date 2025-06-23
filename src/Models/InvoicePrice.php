<?php

namespace Sharenjoy\NoahShop\Models;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sharenjoy\NoahShop\Enums\InvoicePriceType;
use Sharenjoy\NoahShop\Models\Invoice;
use Sharenjoy\NoahShop\Models\Order;
use Sharenjoy\NoahShop\Models\Promo;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahShop\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;

class InvoicePrice extends Model
{
    use CommonModelTrait;
    use LogsActivity;

    protected $casts = [
        'type' => InvoicePriceType::class,
    ];

    protected array $sort = [
        'created_at' => 'desc',
    ];

    protected function formFields(): array
    {
        return [];
    }

    protected function tableFields(): array
    {
        return [
            'promo.title' => ['alias' => 'belongs_to', 'label' => 'promo', 'relation' => 'promo'],
            'user.name' => ['alias' => 'belongs_to', 'label' => 'administrator', 'relation' => 'user', 'relation_column' => 'admin_id'],
            'type' => TextColumn::make('type')
                ->label(__('noah-shop::noah-shop.invoice_price_type'))
                ->sortable()
                ->searchable()
                ->badge(InvoicePriceType::class),
            'value' => ['type' => 'number', 'label' => 'invoice_price_value', 'summarize' => ['sum']],
            'invoice.currency' => ['label' => 'currency'],
            'content' => [],
            'created_at' => ['isToggledHiddenByDefault' => false],
            'updated_at' => ['isToggledHiddenByDefault' => true],
        ];
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

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /** SCOPES */

    /** EVENTS */

    /** OTHERS */
}
