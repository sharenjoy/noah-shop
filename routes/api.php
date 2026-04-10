<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Sharenjoy\NoahShop\Actions\Shop\ResolveSaveUserCoupon;
use Sharenjoy\NoahShop\Models\Promo;

Route::prefix('api')->middleware(['auth:sanctum', 'throttle:10,1'])->group(function () {
    Route::get('/coupon/save', function (Request $request) {
        try {
            $couponCode = $request->input('couponCode');
            $userId = $request->input('userId');

            $promo = Promo::whereType('coupon')->whereCode($couponCode)->firstOrFail();
            $response = ResolveSaveUserCoupon::run($promo, $userId);
        } catch (\Throwable $th) {
            $response = [false, $th->getMessage()];
        }

        return $response;
    });
});
