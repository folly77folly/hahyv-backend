<?php
namespace App\Handler;

use Illuminate\Http\Request;
use App\Handler\RespondsToWebhook;
use Spatie\WebhookClient\WebhookConfig;

class PaystackResponse implements RespondsToWebhook {

    public function respondToValidWebhook(Request $request, WebhookConfig $config)
    {
        http_response_code(200);
    }
}