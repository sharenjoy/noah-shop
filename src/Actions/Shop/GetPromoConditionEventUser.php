<?php

namespace Sharenjoy\NoahShop\Actions\Shop;

use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use Sharenjoy\NoahShop\Actions\Shop\GetDeCryptExtendCondition;
use Sharenjoy\NoahShop\Enums\PromoAutoGenerateType;
use Sharenjoy\NoahShop\Models\Promo;
use Sharenjoy\NoahShop\Models\User;

class GetPromoConditionEventUser
{
    use AsAction;

    /**
     * @param Promo $promo
     * @return Collection
     */
    public function handle(Promo $promo): Collection
    {
        // 如果沒有設定使用者對象，就全部使用者都可以使用
        $users = $promo->userObjectives->isEmpty() ? collect(User::all()) : $promo->userObjectives->pluck('users')->flatten()->unique('id');

        // 加入事件條件篩選使用者
        // if ($promo->auto_generate_type != PromoAutoGenerateType::Never) {
        //     $conditionCode = GetDeCryptExtendCondition::run('promo', $promo->auto_generate_event);

        //     if (!$conditionCode) {
        //         throw new \Exception(__('noah-shop::noah-shop.shop.promo.title.no_condition_code'));
        //     }

        //     $users = $users->filter(eval("return $conditionCode;"));
        // }

        return $users;
    }
}
