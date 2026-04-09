<?php

namespace Sharenjoy\NoahShop\Actions\Shop;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use Sharenjoy\NoahShop\Actions\Shop\GetPromoConditionEventUser;
use Sharenjoy\NoahShop\Enums\UserCouponStatus;
use Sharenjoy\NoahShop\Exceptions\UserHasPromoCouponAlready;
use Sharenjoy\NoahShop\Exceptions\UserNotAllowPromoCouponAssigned;
use Sharenjoy\NoahShop\Models\Promo;
use Sharenjoy\NoahShop\Models\User;
use Sharenjoy\NoahShop\Models\UserCoupon;
use Sharenjoy\NoahShop\Resources\Shop\CouponPromoResource;

class ResolveSaveUserCoupon
{
    use AsAction;

    protected Promo $promo;
    protected Collection $users;
    protected string $divider;
    protected $userId;
    protected $user;

    /**
     * @param Promo $promo
     * @param int $userId 傳入使用者ID，就只判斷這個使用者是否符合條件事件篩選
     * @return array
     */
    public function handle(Promo $promo, $userId): array
    {
        if (!$promo->online) {
            return [false, __('noah-shop::noah-shop.shop.promo.title.notallowed_assign')];
        }

        try {
            $this->promo = $promo;
            $this->users = GetPromoConditionEventUser::run($promo);
            $this->divider = config('noah-shop.promo.coupon_divider');

            // 如果有傳入使用者ID，就只判斷這個使用者是否符合條件事件篩選
            $this->userId = $userId;
            $this->user = $this->users->where('id', $userId)->first();

            if (!$this->user) {
                throw new UserNotAllowPromoCouponAssigned(
                    __('noah-shop::noah-shop.shop.promo.title.notallowed_add_to_user')
                );
            }

            $this->generateNeverCoupon();
        } catch (UserNotAllowPromoCouponAssigned $e) {
            return [false, $e->getMessage()];
        } catch (UserHasPromoCouponAlready $e) {
            return [false, $e->getMessage()];
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('noah-shop::noah-shop.shop.promo.title.add_failed'))
                ->body($e->getMessage())
                ->actions([
                    Action::make('View')->url(CouponPromoResource::getUrl('edit', ['record' => $promo])),
                ])
                ->sendToDatabase(User::superAdmin()->get());

            return [false, __('noah-shop::noah-shop.shop.promo.title.add_failed')];
        }

        return [true, __('noah-shop::noah-shop.shop.promo.title.add_success')];
    }

    protected function generateNeverCoupon()
    {
        $codes = UserCoupon::query()
            ->where('promo_id', $this->promo->id)
            ->get()->pluck('code')->toArray();
        $prefixCode = $this->promo->code . $this->divider;

        // 如果已經有這個優惠券，就不需要再產生了
            $code = $prefixCode . $this->user->id;
            if (in_array($code, $codes)) {
                if ($this->userId) throw new UserHasPromoCouponAlready;
                return;
            }
            $this->createUserCoupon($code);
    }

    protected function createUserCoupon($code)
    {
        $startedAt = now();
        $expiredAt = now()->addMonth();

        // 有效期限的計算
        if ($this->promo->forever && $this->promo->coupon_valid_days) {
            $days = $this->promo->coupon_valid_days;
            $expiredAt = $days % 30 === 0 ? now()->addMonths($days / 30) : now()->addDays($days);
        }

        // 產生優惠券
        $userCoupon = UserCoupon::create([
            'promo_id' => $this->promo->id,
            'user_id' => $this->user->id,
            'code' => $code,
            'started_at' => $this->promo->forever ? $startedAt->format('Y-m-d') : $this->promo->started_at->format('Y-m-d'),
            'expired_at' => $this->promo->forever ? $expiredAt->endOfDay() : $this->promo->expired_at->endOfDay(),
        ]);

        $userCoupon->userCouponStatuses()->create([
            'user_id' => $this->user->id,
            'promo_id' => $this->promo->id,
            'status' => UserCouponStatus::Saved->value,
        ]);
    }
}
