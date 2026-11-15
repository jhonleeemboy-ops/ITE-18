<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

// Sends SMS through TextBee (https://textbee.dev): an Android phone with a SIM
// acts as the gateway. The API key is kept in .env, never in the code.
class SmsSender
{
    // true when an API key has been set in .env
    public function isConfigured(): bool
    {
        return filled(config('services.textbee.key'));
    }

    /**
     * Hand one message to the gateway for many numbers.
     *
     * $numbers are 10-digit Philippine mobile numbers as saved in the database (9171234567).
     * Returns ['sent' => how many the gateway accepted, 'failed' => how many it did not, 'error' => reason or null].
     *
     * "sent" means the gateway accepted the message and queued it on the phone.
     * It does not prove that the text reached the resident's phone.
     */
    public function send(array $numbers, string $message): array
    {
        $sent = 0;
        $error = null;

        // send in groups of 50 so one request never gets too big
        foreach (array_chunk($numbers, 50) as $group) {
            try {
                $payload = [
                    'recipients' => array_map(fn ($number) => '+63' . $number, $group),
                    'message'    => $message,
                ];

                // optional settings from .env: which phone, and which SIM (0 = SIM 1, 1 = SIM 2)
                if (filled(config('services.textbee.device'))) {
                    $payload['deviceId'] = config('services.textbee.device');
                }
                if (filled(config('services.textbee.sim'))) {
                    $payload['simSlotIndex'] = (int) config('services.textbee.sim');
                }

                $response = Http::withHeaders(['x-api-key' => config('services.textbee.key')])
                    ->timeout(20)
                    ->post(config('services.textbee.url') . '/api/v1/gateway/send-sms', $payload);

                if ($response->successful()) {
                    $sent += count($group);
                } else {
                    $error = $this->reason($response->status());
                }
            } catch (\Throwable $e) {
                // no internet, timeout, wrong address…
                $error = 'Could not reach the SMS gateway.';
            }
        }

        return ['sent' => $sent, 'failed' => count($numbers) - $sent, 'error' => $error];
    }

    // Turn the gateway's status code into a message staff can understand
    private function reason(int $status): string
    {
        return match ($status) {
            400     => 'The gateway phone is not ready. Open the TextBee app on the phone and check that it is enabled.',
            401     => 'The SMS API key is wrong or was revoked.',
            404     => 'The gateway phone (device ID) was not found on the TextBee account.',
            429     => 'The SMS plan limit was reached. Nothing more can be sent for now.',
            default => "The SMS gateway returned an error ({$status}).",
        };
    }
}
