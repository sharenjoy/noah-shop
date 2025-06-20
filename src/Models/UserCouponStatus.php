<?php

namespace Sharenjoy\NoahShop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sharenjoy\NoahShop\Enums\UserCouponStatus as UserCouponStatusEnum;
use Sharenjoy\NoahShop\Models\Order;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahShop\Models\User;
use Sharenjoy\NoahShop\Models\UserCoupon;
use Spatie\Activitylog\Traits\LogsActivity;

class UserCouponStatus extends Model
{
    use CommonModelTrait;
    use LogsActivity;

    protected $casts = [
        'status' => UserCouponStatusEnum::class,
    ];

    public $translatable = [];

    protected array $sort = [
        'created_at' => 'desc',
        'id' => 'desc',
    ];

    protected function formFields(): array
    {
        return [];
    }

    protected function tableFields(): array
    {
        return [
            'order.sn' =>  ['alias' => 'belongs_to', 'label' => 'order', 'relation' => 'order', 'relation_route' => 'shop.orders', 'relation_column' => 'order_id', 'operation' => 'view'],
            'status' => ['model' => 'UserCouponStatus'],
            'created_at' => [],
            'updated_at' => [],
        ];
    }

    /** RELACTIONS */

    public function userCoupon(): BelongsTo
    {
        return $this->belongsTo(UserCoupon::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** SCOPES */

    /** EVENTS */

    /** SEO */
}
