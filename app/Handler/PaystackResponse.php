<?php
namespace App\Handler;

use App\Handler\RespondsToWebhook;

class PaystackResponse implements RespondsToWebhook {

    public function respondToValidWebhook(Request $request, WebhookConfig $config)
    {
        http_response_code(200);
    }
}