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

        return response()->json($response);
    });

    // 如果session裡面沒有儲存currency，則從使用者表裡面的preferences取得currency
    Route::get('/currency', function (Request $request) {
        try {
            $currency = session('currency');

            if (!$currency) {
                $user = $request->user();
                $preferences = $user->preferences ?? [];
                $currency = $preferences['currency'] ?? config('currency.default');
            }

            $response = [true, $currency];
        } catch (\Throwable $th) {
            $response = [false, $th->getMessage()];
        }

        return response()->json($response);
    });
});
