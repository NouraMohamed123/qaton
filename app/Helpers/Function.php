<?php

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
//////////////////////////search in area

if (!function_exists('apply_discount')) {
    function apply_discount($nights)
    {
        if ($nights < 1) {
            return 0;
        }
        $discountValue = 0;

        if ($nights >= 7 && $nights < 30) {
            $discountValue = Discount::where('type', 'weekly')->value('value');
        } elseif ($nights >= 30) {
            $discountValue = Discount::where('type', 'monthly')->value('value');
        }

        return $discountValue ?? 0;
    }
    }
