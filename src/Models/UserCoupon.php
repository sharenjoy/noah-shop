<?php

namespace Sharenjoy\NoahShop\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahShop\Models\Promo;
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

    /** SCOPES */

    // 可使用的優惠券
    public function scopeAvailableCoupons($query): Builder
    {
        // 在user_coupon_statuses裡最新的status不等於used的優惠券，且大於started_at，小於expired_at的優惠券
        return $query
        ->where('started_at', '<=', now())
        ->where('expired_at', '>=', now())
        ->whereHas('userCouponStatuses', function ($query) {
            $query->latest()->where('status', '!=', 'used');
        });
    }

    // 即將到期的優惠券
    public function scopeExpiringSoonCoupons($query): Builder
    {
        // 可使用的優惠券，但3天內就要過期了
        return $query->availableCoupons()->whereBetween('expired_at', [now(), now()->addDays(3)]);
    }

    // 已使用的優惠券
    public function scopeUsedCoupons($query): Builder
    {
        // 在user_coupon_statuses裡最新的status等於used的優惠券
        return $query
        ->whereHas('userCouponStatuses', function ($query) {
            $query->latest()->where('status', 'used');
        });
    }

    // 已過期的優惠券
    public function scopeExpiredCoupons($query): Builder
    {
        // 超過expired_at日期的優惠券，而且在user_coupon_statuses裡最新的status不等於used的優惠券
        return $query
        ->where('expired_at', '<', now())
        ->whereHas('userCouponStatuses', function ($query) {
            $query->latest()->where('status', '!=', 'used');
        });
    }
}
