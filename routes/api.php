<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Sharenjoy\NoahShop\Actions\Shop\ResolveSaveUserCoupon;
use Sharenjoy\NoahShop\Models\Promo;

Route::prefix('api')->group(function () {
    Route::post('/tokens/create', function (Request $request) {
        $user = Auth::loginUsingId(1);
        $token = $user->createToken('api-token');

        return ['token' => $token->plainTextToken];
    });

    Route::get('/coupon/save', function (Request $request) {
        $couponCode = $request->input('couponCode');
        $userId = $request->input('userId');

        try {
            $promo = Promo::whereType('coupon')->whereCode($couponCode)->firstOrFail();
            $response = ResolveSaveUserCoupon::run($promo, $userId);
        } catch (\Throwable $th) {
            $response = [false, $th->getMessage()];
        }


        return $response;
    })->middleware(['auth:sanctum']);
});
