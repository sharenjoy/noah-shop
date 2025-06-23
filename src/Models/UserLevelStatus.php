<?php

namespace Sharenjoy\NoahShop\Models;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sharenjoy\NoahShop\Enums\UserLevelStatus as UserLevelStatusEnum;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahShop\Models\User;
use Sharenjoy\NoahShop\Models\UserLevel;
use Spatie\Activitylog\Traits\LogsActivity;

class UserLevelStatus extends Model
{
    use CommonModelTrait;
    use LogsActivity;

    protected $casts = [
        'status' => UserLevelStatusEnum::class,
        'started_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public $translatable = [];

    protected array $sort = [
        'created_at' => 'desc',
        'id' => 'desc',
    ];

    protected static function boot()
    {
        parent::boot();

        // 用戶等級狀態更新以後，如果狀態是開啟的確保同一個使用者其他狀態狀態是關閉的
        static::saved(function (UserLevelStatus $userLevelStatus) {
            if ($userLevelStatus->status === UserLevelStatusEnum::On) {
                UserLevelStatus::where('id', '!=', $userLevelStatus->id)
                    ->where('user_id', $userLevelStatus->user_id)
                    ->update(['status' => UserLevelStatusEnum::Off]);
            }
        });
    }

    protected function formFields(): array
    {
        return [
            'left' => [
                'status' => Section::make('')->schema([
                    Select::make('status')
                        ->label(__('noah-shop::noah-shop.status'))
                        ->required()
                        ->options(UserLevelStatusEnum::class),
                ]),
                'started_at' => ['label' => 'shop.promo.title.started_at', 'alias' => 'PublishedAt'],
                'expired_at' => ['label' => 'shop.promo.title.expired_at', 'alias' => 'PublishedAt'],
            ],
            'right' => [],
        ];
    }

    protected function tableFields(): array
    {
        return [
            'userLevel.title' =>  ['description' => true, 'alias' => 'belongs_to', 'label' => 'user_level', 'relation' => 'userLevel', 'relation_route' => 'shop.user-levels', 'relation_column' => 'user_level_id'],
            'user.name' =>  ['description' => true, 'alias' => 'belongs_to', 'label' => 'user', 'relation' => 'user'],
            'status' => ['model' => 'UserLevel'],
            'started_at' => ['label' => 'shop.promo.title.started_at'],
            'expired_at' => ['label' => 'shop.promo.title.expired_at'],
            'created_at' => ['isToggledHiddenByDefault' => true],
            'updated_at' => ['isToggledHiddenByDefault' => true],
        ];
    }

    /** RELACTIONS */

    public function userLevel(): BelongsTo
    {
        return $this->belongsTo(UserLevel::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** SCOPES */

    /** EVENTS */

    /** SEO */
}
