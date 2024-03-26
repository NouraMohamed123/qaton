<?php

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

// if (!function_exists('apply_discount')) {
//     function apply_discount($nights)
//     {
//           if($nights>= 7 && $nights < 30){
//             discounts::where('')
//           }elseif($nights>= 30){

//           }
//           return 0;
//     }
//     }
