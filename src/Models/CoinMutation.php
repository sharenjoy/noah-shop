<?php

namespace Sharenjoy\NoahShop\Models;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sharenjoy\NoahShop\Enums\CoinType;
use Sharenjoy\NoahShop\Models\Order;
use Sharenjoy\NoahShop\Models\Promo;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class CoinMutation extends Model
{
    use CommonModelTrait;
    use LogsActivity;

    protected $casts = [
        'type' => CoinType::class,
    ];

    protected $fillable = [
        'promo_id',
        'order_id',
        'coinable_type',
        'coinable_id',
        'reference_type',
        'reference_id',
        'type',
        'amount',
        'description',
    ];

    /**
     * CoinMutation constructor.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable('coin_mutations');
    }

    protected function formFields(): array
    {
        return [
            'left' => [
                'amount' => Section::make()->schema([
                    TextInput::make('amount')
                        ->numeric()
                        ->placeholder(__('noah-cms::noah-cms.user_coin'))
                        ->label(__('noah-cms::noah-cms.user_coin'))
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
            'promo.title' => ['alias' => 'belongs_to', 'label' => 'promo', 'relation' => 'promo'],
            'type' => TextColumn::make('type')
                ->label(__('noah-cms::noah-cms.type'))
                ->sortable()
                ->searchable()
                ->badge(CoinType::class),
            'amount' => ['type' => 'number', 'label' => 'user_coin', 'summarize' => ['sum']],
            'reference.name' => ['alias' => 'belongs_to', 'label' => 'coin_reference', 'relation' => 'reference', 'relation_route' => 'users', 'relation_column' => 'reference'],
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
    public function coinable()
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

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
