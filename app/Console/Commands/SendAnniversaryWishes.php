<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Family;
use App\Models\FamilyMember;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class SendAnniversaryWishes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:anniversary-wishes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send anniversary wishes to family members';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();
        $heads = Family::whereMonth('date_of_anniversary', $today->month)
                    ->whereDay('date_of_anniversary', $today->day)
                    ->get();
        
        $members = FamilyMember::whereMonth('date_of_anniversary', $today->month)
                        ->whereDay('date_of_anniversary', $today->day)
                        ->get();
        
        $users = $heads->merge($members);
       // print_r($users);exit;

        foreach ($users as $user) {
            

            $response = Http::post('https://nkybahfpvbf3tlxe5tdzwxnns40tfghu.lambda-url.ap-south-1.on.aws/send-notification', [
                'title' => 'Happy Anniversary!',
                'body' => 'Wishing you a wonderful anniversary filled with love and happiness.',
                'type' => 'birthday',
                'image_url' => "erfheifuoe",
                'mobile_number' => $user->head_mobile_number,
            ]);

          // print_r($response);exit;

        }

        $this->info('Aniiversay wishes sent successfully!');
    }
}
