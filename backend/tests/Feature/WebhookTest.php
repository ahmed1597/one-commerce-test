<?php

use App\Jobs\ProcessShopifyProductWebhook;
use Illuminate\Support\Facades\Queue;

function sign(string $body): string {
    $secret = config('services.shopify.webhook_secret', 'test');
    return base64_encode(hash_hmac('sha256', $body, $secret, true));
}

it('verifies hmac and dispatches job', function () {
    Queue::fake();

    $payload = json_encode(['id' => 111, 'title' => 'From Webhook'], JSON_UNESCAPED_SLASHES);
    $sig = sign($payload);

    $resp = $this->call(
        'POST',
        '/api/webhooks/shopify/products/update',
        [], [], [], 
        [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_SHOPIFY_HMAC_SHA256' => $sig,
        ],
        $payload 
    );

    $resp->assertOk();
    Queue::assertPushed(ProcessShopifyProductWebhook::class);
});
