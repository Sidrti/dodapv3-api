<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class Helper
{
    public static function sendOtpToUser($mobileNumber, $otpNumber)
    {

        $apiKey = '55e2d0fa-561a-11ef-8b60-0200cd936042';
        $mobileNumber = $mobileNumber;
        $senderId = 'OTP1';

        $url = "https://2factor.in/API/V1/{$apiKey}/SMS/{$mobileNumber}/{$otpNumber}/{$senderId}";

        // Make the HTTP GET request to the API
        $response = Http::withoutVerifying()->get($url);
        //$response = Http::get($url);

        // Check if the request was successful
        if ($response->successful()) {
           return true;
        } else {
           return false;
        }
    }
}
