<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Family;
use App\Models\FamilyMember;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
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
        $heads = Family::whereMonth('head_dob', $today->month)
                    ->whereDay('head_dob', $today->day)
                    ->get();
        
        $members = FamilyMember::whereMonth('dob', $today->month)
                        ->whereDay('dob', $today->day)
                        ->get();
        
        $users = $heads->merge($members);

        foreach ($users as $user) {
            

            $response = Http::post('https://nkybahfpvbf3tlxe5tdzwxnns40tfghu.lambda-url.ap-south-1.on.aws/send-notification', [
                'title' => 'Happy Birthday!',
                'body' => 'Wishing you a wonderful birthday filled with love and happiness.',
                'type' => 'birthday',
                'image_url' => "erfheifuoe",
                'mobile_number' => $user->head_mobile_number,
            ]);

          // print_r($response);exit;

        }

        $this->info('Birthday wishes sent successfully!');
    }
}
