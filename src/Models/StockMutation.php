<?php

namespace Sharenjoy\NoahShop\Models;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sharenjoy\NoahShop\Models\Order;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class StockMutation extends Model
{
    use CommonModelTrait;
    use LogsActivity;

    /**
     * CoinMutation constructor.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable('stock_mutations');
    }

    protected function formFields(): array
    {
        return [
            'left' => [
                'amount' => Section::make()->schema([
                    TextInput::make('amount')
                        ->numeric()
                        ->placeholder(__('noah-cms::noah-cms.stock'))
                        ->label(__('noah-cms::noah-cms.stock'))
                        ->helperText('數值可以是正數或負數，正數表示增加，負數表示減少')
                        ->required()
                        ->rules(['required', 'numeric']),
                ])->columns(1),
                'description' => ['rules' => ['string']],
            ],
            'right' => [],
        ];
    }

    protected function tableFields(): array
    {
        return [
            'order.sn' => ['alias' => 'belongs_to', 'label' => 'order_sn', 'relation' => 'order'],
            'amount' => ['type' => 'number', 'label' => 'stock', 'summarize' => ['sum']],
            'reference.name' => ['alias' => 'belongs_to', 'label' => 'stock_reference', 'relation' => 'reference', 'relation_route' => 'users', 'relation_column' => 'reference'],
            'description' => [],
            'created_at' => [],
            'updated_at' => [],
        ];
    }

    /**
     * Relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function stockable()
    {
        return $this->morphTo();
    }

    /**
     * Relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function reference()
    {
        return $this->morphTo();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
