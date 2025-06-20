<?php

namespace Sharenjoy\NoahShop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahShop\Models\User;
use Sharenjoy\NoahShop\Models\UserLevelStatus;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;

class UserLevel extends Model implements Sortable
{
    use CommonModelTrait;
    use LogsActivity;
    use SortableTrait;
    use HasTranslations;
    use SoftDeletes;

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'album' => 'array',
    ];

    public $translatable = [
        'title',
        'description',
        'content',
    ];

    protected array $sort = [
        'order_column' => 'asc',
    ];

    protected static function booted()
    {
        static::saved(function (UserLevel $userLevel) {
            // 如果 is_default 為 true，將同一 user 的其他地址的 is_default 設為 false
            if ($userLevel->is_default) {
                UserLevel::where('id', '!=', $userLevel->id) // 排除當前地址
                    ->update(['is_default' => false]);
            }
        });
    }

    protected function formFields(): array
    {
        return [
            'left' => [
                'title' => ['required' => true, 'rules' => ['required', 'string']],
                'description' => [],
                'content' => ['profile' => 'simple'],
            ],
            'right' => [
                'img' => [],
                'album' => [],
                'is_default' => ['required' => true, 'alias' => 'yesno'],
                'is_active' => ['required' => true],
            ],
        ];
    }

    protected function tableFields(): array
    {
        return [
            'title' => ['description' => true],
            'discount_percent' => ['label' => 'shop.promo.title.discount_percent', 'suffix' => '%'],
            'point_times' => ['label' => 'shop.promo.title.point_times'],
            'level_up_amount' => ['label' => 'shop.promo.title.level_up_amount', 'type' => 'number'],
            'auto_level_up' => ['label' => 'shop.promo.title.auto_level_up', 'type' => 'boolean'],
            'forever' => ['label' => 'shop.promo.title.forever', 'type' => 'boolean'],
            'level_duration' => ['label' => 'shop.promo.title.level_duration', 'suffix' => '年'],
            'relationCount' => ['label' => 'users_count', 'relation' => 'users'],
            'is_default' => ['type' => 'boolean'],
            'is_active' => [],
            'created_at' => ['isToggledHiddenByDefault' => true],
            'updated_at' => ['isToggledHiddenByDefault' => true],
        ];
    }

    /** RELACTIONS */

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function userLevelStatuses(): HasMany
    {
        return $this->hasMany(UserLevelStatus::class);
    }

    /** SCOPES */

    /** EVENTS */

    /** SEO */

    /** OTHERS */
}
