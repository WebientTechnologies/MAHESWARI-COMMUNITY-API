<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Exception;
use Twilio\Rest\Client;

class FamilyOtp extends Model
{
    use HasFactory;
    protected $table = "families_otp";

    protected $fillable =[
        'family_id',
        'otp',
        'expire_at'
    ];

    public function sendSms($receiverNumber){
        $message = 'Login Otp is '.$this->otp;

        try {
            $accounnt_id = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");

            $client  = new Client($accounnt_id, $auth_token);
            // print_r($client);exit;
            $client->messages->create($receiverNumber,[
                'from' => $twilio_number,
                'body' => $message
            ]);

            info("SMS Sent Successfully!");
        } catch (\Exception $e) {
            info("Error: ".$e->getMessage());
        }

    }
}
