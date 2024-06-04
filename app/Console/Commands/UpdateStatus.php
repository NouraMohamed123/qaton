<?php

namespace App\Console\Commands;

use App\Models\Booked_apartment;
use Illuminate\Console\Command;
use App\Models\YourModel; // Replace with your actual model
use Carbon\Carbon;

class UpdateStatus extends Command
{
    protected $signature = 'status:update';
    protected $description = 'Update the status of bookings every minute';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = Carbon::now()->toDateString();
        $bookings = Booked_apartment::all(); // Fetch all bookings, adjust query as needed

        foreach ($bookings as $booking) {
            $dateTo = Carbon::parse($booking->date_to)->toDateString();

            if ($booking->status === 'canceled') {
                $booking->status = 'canceled';
            } elseif ($dateTo < $now) {
                $booking->status = 'past';
            }

            $booking->save();
        }

        $this->info('Booking statuses updated successfully.');
    }
}
