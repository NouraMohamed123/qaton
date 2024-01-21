<?php

use Illuminate\Support\Facades\Storage;

function upload($avatar, $directory)
{
        $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
        $avatar->move($directory, $avatarName);
        return $avatarName;

}
