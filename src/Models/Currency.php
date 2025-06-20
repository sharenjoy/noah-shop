<?php

namespace Sharenjoy\NoahShop\Models;

use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahCms\Models\Traits\HasMediaLibrary;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Currency extends Model
{
    use CommonModelTrait;
    use LogsActivity;
    use SortableTrait;
    use HasMediaLibrary;

    protected $casts = [];

    public $translatable = [];

    protected array $sort = [
        'id' => 'asc',
    ];

    protected function formFields(): array
    {
        return [
            'left' => [
                'name' => ['required' => true, 'disable' => 'edit'],
                'code' => ['alias' => 'text', 'label' => 'currency_code', 'required' => true, 'disable' => 'edit'],
                'symbol' => ['alias' => 'text', 'required' => true, 'disable' => 'edit'],
                'exchange_rate' => ['alias' => 'text', 'required' => true],
            ],
            'right' => [],
        ];
    }

    protected function tableFields(): array
    {
        return [
            'name' => [],
            'code' => ['label' => 'currency_code'],
            'symbol' => [],
            'exchange_rate' => [],
            // 'is_active' => [],
            'created_at' => ['isToggledHiddenByDefault' => true],
            'updated_at' => ['isToggledHiddenByDefault' => true],
        ];
    }

    /** RELACTIONS */

    /** SCOPES */

    /** EVENTS */

    /** SEO */

    /** OTHERS */
}
