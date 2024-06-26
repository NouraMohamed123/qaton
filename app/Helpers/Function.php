<?php

use App\Models\Point;
use App\Models\Discount;
use Illuminate\Support\Facades\Storage;
if (!function_exists('upload')) {
function upload($avatar, $directory)
{
        $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
        $avatar->move($directory, $avatarName);
        return $avatarName;

}
}


if (!function_exists('apply_discount')) {
    function apply_discount($nights)
    {
        if ($nights < 1) {
            return 0;
        }
        $discountValue = 0;

        if ($nights >= 7 && $nights < 28) {
            $discountValue = Discount::where('type', 'weekly')->value('value');
        } elseif ($nights >= 28) {
            $discountValue = Discount::where('type', 'monthly')->value('value');
        }

        return $discountValue ?? 0;
    }
    }

    /////////
    if (!function_exists('checkCoupon')) {
        function checkCoupon($couponCode, $totalAmount)
        {
            $coupon = App\Models\Coupon::where('discount_code', $couponCode)->first();

            if (!$coupon) {
                return ['status' => false, 'message' => 'coupon not exist'];
            }

            $currentDate = date('Y-m-d');
            if ($currentDate < $coupon->start_date || $currentDate > $coupon->end_date) {
                return ['status' => false, 'message' => 'date expired in coupon'];
            }

            if ($coupon->max_usage !== null && $coupon->max_usage <= 0) {
                return ['status' => false, 'message' => 'max usage reached in coupon'];
            }

            if ($coupon->max_discount_value !== null && $totalAmount > $coupon->max_discount_value) {
                return ['status' => false, 'message' => 'totalAmount greater than max discount value available in coupon'];
            }

            // Decrement max_usage in the database
            // if ($coupon->max_usage !== null) {
            //     $coupon->decrement('max_usage');
            // }

            if ($coupon->type == 'percentage') {
                $discount = (float) $coupon->discount_percentage;
                $priceAfterDiscount = $totalAmount - ($totalAmount * $discount);
            } else {
                $discount = (int) $coupon->discount;
                $priceAfterDiscount = $totalAmount - $discount;
            }

            return [
                'status' => true,
                'discount' => $discount,
                'price_after_discount' => $priceAfterDiscount,
                'id' => $coupon->id,
            ];
        }
    }
    if (!function_exists('calculateRiyalsFromPoints')) {
    function calculateRiyalsFromPoints($userId)
{
    $points = Point::where('user_id', $userId)->sum('point');
    $pointsPerRiyal = 5000;
    $amountPerRiyal = 100;

    if ($points > 0) {
        $riyals = ($points / $pointsPerRiyal) * $amountPerRiyal;
        return $riyals;
    }

    return 0;
}
    }
