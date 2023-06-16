<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Family;
use App\Models\FamilyMember;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class SendBirthdayNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:birthday-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send birthday Notification to family members';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();
        
        $familyMembers = FamilyMember::whereDay('dob', $today->day)
            ->whereMonth('dob', $today->month)
            ->get();
            //print_r($familyMembers);exit;
        foreach ($familyMembers as $familyMember) {
            // print_r($familyMember->family_id);exit;
            $name = $familyMember->first_name. ''. $familyMember->middle_name. ''.$familyMember->last_name;
           
            $relatedFamilyMembers = FamilyMember::where('family_id', $familyMember->family_id)
                ->where('id', '!=', $familyMember->id)
                ->get();

            foreach ($relatedFamilyMembers as $relatedFamilyMember) {
                //print_r($name);exit;
                $response = Http::post('https://nkybahfpvbf3tlxe5tdzwxnns40tfghu.lambda-url.ap-south-1.on.aws/send-notification', [
                    'title' => 'Happy Birthday!',
                    'body' => 'Today is '. $name. ' birthday',
                    'type' => 'birthday',
                    'image_url' => "erfheifuoe",
                    'mobile_number' => $relatedFamilyMember->mobile_number,
                ]);
            }

            // Send notification to family head
            $family = Family::find($familyMember->family_id);
            // print_r($family);exit;
            if ($family) {
                $response = Http::post('https://nkybahfpvbf3tlxe5tdzwxnns40tfghu.lambda-url.ap-south-1.on.aws/send-notification', [
                    'title' => 'Happy Birthday!',
                    'body' => 'Today is '. $name. ' birthday',
                    'type' => 'birthday',
                    'image_url' => "erfheifuoe",
                    'mobile_number' => $family->head_mobile_number,
                ]);

            }
        }

        $families = Family::whereMonth('head_dob', $today->month)
                    ->whereDay('head_dob', $today->day)
                    ->get();

        foreach ($families as $family) {
            // print_r($familyMember->family_id);exit;
            $name = $family->head_first_name. ''. $family->head_middle_name. ''.$family->head_last_name;
            
            $relatedFamilyMembers = FamilyMember::where('family_id', $family->id)->get();

            foreach ($relatedFamilyMembers as $relatedFamilyMember) {
                //print_r($name);exit;
                $response = Http::post('https://nkybahfpvbf3tlxe5tdzwxnns40tfghu.lambda-url.ap-south-1.on.aws/send-notification', [
                    'title' => 'Happy Birthday!',
                    'body' => 'Today is '. $name. ' birthday',
                    'type' => 'birthday',
                    'image_url' => "erfheifuoe",
                    'mobile_number' => $relatedFamilyMember->mobile_number,
                ]);
            }

        }

        $this->info('Birthday notifications sent successfully!');
    }
}
