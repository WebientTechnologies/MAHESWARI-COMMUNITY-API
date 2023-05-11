<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Family;
use Carbon\Carbon;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class SendBirthdayWishes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:birthday-wishes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send birthday wishes to family members';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();
        $users = Family::whereMonth('head_dob', $today->month)
                    ->whereDay('head_dob', $today->day)
                    ->get();
        

        foreach ($users as $user) {
            // print_r($user->device_token);exit;

            $notification = [
                'title' => 'Happy Birthday!',
                'body' => 'Wishing you a wonderful birthday filled with love and happiness.',
            ];

            $message = CloudMessage::withTarget('token', $user->device_token)
                ->withNotification($notification);

            Firebase::messaging()->send($message);

        }

        $this->info('Birthday wishes sent successfully!');
    }
}
