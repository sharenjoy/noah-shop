<?php

use Illuminate\Support\Facades\Route;
use Sharenjoy\NoahShop\Models\Promo;
use Sharenjoy\NoahShop\Models\UserCoupon;
use Sharenjoy\NoahShop\Notifications\UserCouponCreated;

// Route::prefix('noah-shop/notification')->group(function () {
//     Route::get('/user-coupon-created', function () {
//         $userCoupon = UserCoupon::find(1);
//         $promo = Promo::find(4);

//         return (new UserCouponCreated($userCoupon, $promo))
//             ->toMail($userCoupon->user);
//     });
// });
