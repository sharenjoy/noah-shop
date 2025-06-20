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
use Sharenjoy\NoahShop\Notifications\UserCouponCreated;
use Sharenjoy\NoahShop\Resources\Shop\CouponPromoResource;

class ResolveGenerateUserCoupon
{
    use AsAction;

    protected Promo $promo;
    protected Collection $users;
    protected string $divider;
    protected $userId;

    /**
     * @param Promo $promo
     * @param int|null $userId 如果有傳入使用者ID，就只判斷這個使用者是否符合條件事件篩選
     * @return array
     */
    public function handle(Promo $promo, $userId = null): array
    {
        if (!$promo->generatable) {
            return [false, __('noah-shop::noah-shop.shop.promo.title.notallowed_generatable')];
        }

        try {
            $this->promo = $promo;
            $this->users = GetPromoConditionEventUser::run($promo);
            $this->divider = config('noah-shop.promo.coupon_divider');

            // 如果有傳入使用者ID，就只判斷這個使用者是否符合條件事件篩選
            if ($userId) {
                $this->userId = $userId;
                $this->users = $this->users->where('id', $userId);

                if ($this->users->isEmpty()) {
                    throw new UserNotAllowPromoCouponAssigned(
                        __('noah-shop::noah-shop.shop.promo.title.notallowed_generatable_to_user')
                    );
                }
            }

            $method = 'generate' . ucfirst($this->promo->auto_generate_type->value) . 'Coupon';
            $this->$method();
        } catch (UserNotAllowPromoCouponAssigned $e) {
            return [false, $e->getMessage()];
        } catch (UserHasPromoCouponAlready $e) {
            return [false, $e->getMessage()];
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('noah-shop::noah-shop.shop.promo.title.generate_failed'))
                ->body($e->getMessage())
                ->actions([
                    Action::make('View')->url(CouponPromoResource::getUrl('edit', ['record' => $promo])),
                ])
                ->sendToDatabase(User::superAdmin()->get());

            return [false, __('noah-shop::noah-shop.shop.promo.title.generate_failed')];
        }

        return [true, __('noah-shop::noah-shop.shop.promo.title.generate_success')];
    }

    protected function generateNeverCoupon()
    {
        $codes = UserCoupon::query()
            ->where('promo_id', $this->promo->id)
            ->get()->pluck('code')->toArray();
        $prefixCode = $this->promo->code . $this->divider;

        // 如果已經有這個優惠券，就不需要再產生了
        foreach ($this->users as $user) {
            $code = $prefixCode . $user->id;
            if (in_array($code, $codes)) {
                if ($this->userId) throw new UserHasPromoCouponAlready;
                continue;
            }
            $this->createUserCoupon($code, $user);
        }
    }

    protected function generateYearlyCoupon()
    {
        // 如果是每年自動產生優惠券，且今天的日期符合自動產生的日期，就產生優惠券
        if (now()->format('m-d') != $this->promo->auto_generate_date) {
            return;
        }

        // 產生優惠券的前綴碼
        $prefixCode = $this->promo->code . $this->divider . now()->format('Y') . $this->divider;
        // 取得已經產生的優惠券
        $codes = UserCoupon::query()
            ->where('promo_id', $this->promo->id)
            ->where('code', 'like', $prefixCode . '%') // 使用 like 篩選符合 prefixCode 的優惠券
            ->get()->pluck('code')->toArray();

        foreach ($this->users as $user) {
            $code = $prefixCode . $user->id;
            if (in_array($code, $codes)) {
                if ($this->userId) throw new UserHasPromoCouponAlready;
                continue;
            }
            $this->createUserCoupon($code, $user);
        }
    }

    protected function generateMonthlyCoupon()
    {
        if (now()->format('d') != $this->promo->auto_generate_day) {
            return;
        }

        // 產生優惠券的前綴碼
        $prefixCode = $this->promo->code . $this->divider . now()->format('Ym') . $this->divider;
        // 取得已經產生的優惠券
        $codes = UserCoupon::query()
            ->where('promo_id', $this->promo->id)
            ->where('code', 'like', $prefixCode . '%') // 使用 like 篩選符合 prefixCode 的優惠券
            ->get()->pluck('code')->toArray();

        foreach ($this->users as $user) {
            $code = $prefixCode . $user->id;
            if (in_array($code, $codes)) {
                if ($this->userId) throw new UserHasPromoCouponAlready;
                continue;
            }
            $this->createUserCoupon($code, $user);
        }
    }

    protected function generateEverydayCoupon()
    {
        $prefixCode = $this->promo->code . $this->divider . now()->format('Ymd') . $this->divider;

        $codes = UserCoupon::query()
            ->where('promo_id', $this->promo->id)
            ->where('code', 'like', $prefixCode . '%') // 使用 like 篩選符合 prefixCode 的優惠券
            ->get()->pluck('code')->toArray();

        foreach ($this->users as $user) {
            $code = $prefixCode . $user->id;
            if (in_array($code, $codes)) {
                if ($this->userId) throw new UserHasPromoCouponAlready;
                continue;
            }
            $this->createUserCoupon($code, $user);
        }
    }

    protected function createUserCoupon($code, $user)
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
            'user_id' => $user->id,
            'code' => $code,
            'started_at' => $this->promo->forever ? $startedAt->format('Y-m-d') : $this->promo->started_at->format('Y-m-d'),
            'expired_at' => $this->promo->forever ? $expiredAt->endOfDay() : $this->promo->expired_at->endOfDay(),
        ]);

        $userCoupon->userCouponStatuses()->create([
            'user_id' => $user->id,
            'promo_id' => $this->promo->id,
            'status' => UserCouponStatus::Assigned->value,
        ]);

        // 發送通知
        $user->notify(new UserCouponCreated($userCoupon, $this->promo));
    }

    public function asJob(Promo $promo): void
    {
        $this->handle($promo);
    }
}
