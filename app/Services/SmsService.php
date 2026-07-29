<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send login OTP SMS via YourBulkSMS.
     *
     * @return array{ok: bool, response: string|null, url: string}
     */
    public static function sendLoginOtp(string $mobile, string $otp): array
    {
        $authKey = env('SMS_AUTH_KEY', '3939576f726c6433313263');
        $sender = env('SMS_SENDER', 'RLTREW');
        $dltTeId = env('SMS_DLT_TE_ID', '1707174350721246613');

        $message = $otp
            . ' is your Login one-time password for Relationship Revive.'
            . 'Please use it within 10 minutes. Keep it secure and private. - Relationship Revive';

        $url = 'http://control.yourbulksms.com/api/sendhttp.php?' . http_build_query([
            'authkey' => $authKey,
            'mobiles' => $mobile,
            'message' => $message,
            'sender' => $sender,
            'route' => 2,
            'country' => 0,
            'DLT_TE_ID' => $dltTeId,
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($response === false || $error) {
            Log::warning('SMS send failed', [
                'mobile' => $mobile,
                'error' => $error,
                'httpCode' => $httpCode,
            ]);

            return [
                'ok' => false,
                'response' => $error ?: 'SMS gateway request failed',
                'url' => $url,
            ];
        }

        Log::info('SMS send response', [
            'mobile' => $mobile,
            'httpCode' => $httpCode,
            'response' => $response,
        ]);

        // Gateway usually returns a message id / numeric success string
        $ok = $httpCode >= 200 && $httpCode < 300 && !empty($response)
            && stripos((string) $response, 'error') === false
            && stripos((string) $response, 'invalid') === false;

        return [
            'ok' => $ok,
            'response' => (string) $response,
            'url' => $url,
        ];
    }
}
