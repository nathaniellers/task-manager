<?php

namespace App\Mail\Transport;

use Illuminate\Mail\Transport\Transport as BaseTransport;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Http;

class MailtrapTransport extends BaseTransport
{
    protected $apiToken;

    public function __construct($apiToken)
    {
        $this->apiToken = $apiToken;
    }

    public function send(Mailable $mailable, array $options = [])
    {
        $message = $this->createMessage($mailable);

        // Example API call to Mailtrap (this is pseudocode)
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken,
        ])->post('https://api.mailtrap.io/api/v1/send', [
            'subject' => $message->getSubject(),
            'from' => $message->getFrom(),
            'to' => $message->getTo(),
            'body' => $message->getBody(),
        ]);

        // Handle the API response
        if ($response->successful()) {
            return $response->json();
        } else {
            throw new \Exception("Failed to send email.");
        }
    }
}
