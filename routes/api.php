<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/tokens/create', function (Request $request) {
    $user = Auth::loginUsingId(1);
    $token = $user->createToken('api-token');

    return ['token' => $token->plainTextToken];
});

Route::get('/coupon/save', function (Request $request) {
    $couponCode = $request->get('couponCode');
    $userId = $request->get('userId');

    try {
        $promo = \Sharenjoy\NoahShop\Models\Promo::whereType('coupon')->whereCode($couponCode)->firstOrFail();
        $response = \Sharenjoy\NoahShop\Actions\Shop\ResolveSaveUserCoupon::run($promo, $userId);
    } catch (\Throwable $th) {
        $response = [false, $th->getMessage()];
    }


    return $response;
})->middleware(['auth:sanctum']);


