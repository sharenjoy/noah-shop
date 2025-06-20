<?php

namespace Sharenjoy\NoahShop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sharenjoy\NoahShop\Enums\ObjectiveStatus;
use Sharenjoy\NoahShop\Enums\ObjectiveType;
use Sharenjoy\NoahShop\Models\Product;
use Sharenjoy\NoahShop\Models\Promo;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahCms\Tables\Columns\LoadingStatusColumn;
use Spatie\Activitylog\Traits\LogsActivity;

class Objective extends Model
{
    use CommonModelTrait;
    use LogsActivity;
    use SoftDeletes;

    protected $casts = [
        'type' => ObjectiveType::class,
        'status' => ObjectiveStatus::class,
        'user' => 'array',
        'product' => 'array',
        'generated_at' => 'datetime',
    ];

    protected array $sort = [
        'created_at' => 'desc',
    ];

    protected array $formFields = [
        'left' => [
            'title' => [
                'required' => true,
                'rules' => ['required', 'string'],
            ],
            'description' => [],
        ],
        'right' => [],
    ];

    protected function tableFields(): array
    {
        return [
            'title' => ['description' => true],
            'type' => ['alias' => 'status', 'label' => 'type', 'model' => 'objective'],
            'relation_count' => ['label' => 'products_count', 'relation' => 'products'],
            'user_relation_count' => ['alias' => 'relation_count', 'label' => 'users_count', 'relation' => 'users'],
            'status' => LoadingStatusColumn::make('status')
                ->label(__('noah-cms::noah-cms.status'))
                ->toggleable(),
            'generated_at' => ['label' => 'last_generated_at', 'type' => 'date'],
            'created_at' => [],
            'updated_at' => [],
        ];
    }

    /** RELACTIONS */

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'objectiveable')->with(['validOrders']);
    }

    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'objectiveable');
    }

    public function promos(): MorphToMany
    {
        return $this->morphToMany(Promo::class, 'promoable');
    }

    /** SCOPES */

    /** EVENTS */

    /** OTHERS */
}
