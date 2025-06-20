<?php

namespace Sharenjoy\NoahShop\Commands;

use Illuminate\Console\Command;
use Sharenjoy\NoahShop\Actions\Shop\ResolveGenerateUserCoupon;
use Sharenjoy\NoahShop\Enums\PromoType;
use Sharenjoy\NoahShop\Models\Promo;

class GenerateCouponPromos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'noah-shop:generate-coupon-promos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate coupon promos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $promos = Promo::with(['userObjectives.users', 'coupons'])->where('type', PromoType::Coupon)->get()->where('generatable', true);

        foreach ($promos as $promo) {
            dispatch(ResolveGenerateUserCoupon::makeJob($promo));
        }

        $this->info('Generate coupon promos successfully.');
    }
}
