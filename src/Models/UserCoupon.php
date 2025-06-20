<?php

namespace Sharenjoy\NoahShop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sharenjoy\NoahShop\Models\Promo;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahShop\Models\User;
use Sharenjoy\NoahShop\Models\UserCouponStatus;
use Spatie\Activitylog\Traits\LogsActivity;

class UserCoupon extends Model
{
    use CommonModelTrait;
    use LogsActivity;
    use SoftDeletes;

    protected $casts = [
        'started_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    protected array $formFields = [];

    protected array $tableFields = [
        'promo.title' =>  ['description' => true, 'alias' => 'belongs_to', 'label' => 'promo', 'relation' => 'promo', 'relation_route' => 'shop.coupon-promos', 'relation_column' => 'promo_id'],
        'user.name' =>  ['description' => true, 'alias' => 'belongs_to', 'label' => 'user', 'relation' => 'user'],
        'code' => ['label' => 'coupon_promo'],
        'started_at' => ['label' => 'shop.promo.title.started_at'],
        'expired_at' => ['label' => 'shop.promo.title.expired_at'],
        'created_at' => ['isToggledHiddenByDefault' => true],
        'updated_at' => ['isToggledHiddenByDefault' => true],
    ];

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userCouponStatuses(): HasMany
    {
        return $this->hasMany(UserCouponStatus::class);
    }
}
