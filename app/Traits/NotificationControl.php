<?php

namespace App\Traits;

use App\Models\ControlNotification;

trait NotificationControl
{
    public function controlNotification($type)
    {
        $message = '';
        $time = '';
        $title = '';

        if ($type == 'booking') {
            $message = ControlNotification::where('type', 'booking')->value('message');
            $title = ControlNotification::where('type', 'booking')->value('title');
            $time = ControlNotification::where('type', 'booking')->value('time');
        } elseif ($type == 'entry_day') {
            $message = ControlNotification::where('type', 'entry_day')->value('message');
            $title = ControlNotification::where('type', 'entry_day')->value('title');
            $time = ControlNotification::where('type', 'entry_day')->value('time');
        } elseif ($type == 'exit_day') {
            $message = ControlNotification::where('type', 'exit_day')->value('message');
            $title = ControlNotification::where('type', 'exit_day')->value('title');
            $time = ControlNotification::where('type', 'exit_day')->value('time');
        } else {
            $message = 'Default message';
            $title = 'Default title';
            $time = 'Default time';
        }

        return ['message' => $message, 'time' => $time, 'title' => $title];
    }
}
