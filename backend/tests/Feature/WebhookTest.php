<?php
use App\Jobs\ProcessShopifyProductWebhook;
use Illuminate\Support\Facades\Queue;

function hmac(string $body): string {
    $secret = config('services.shopify.webhook_secret', 'test');
    return base64_encode(hash_hmac('sha256', $body, $secret, true));
}

it('verifies hmac and dispatches job', function () {
    Queue::fake();
    $payload = json_encode(['id' => 111, 'title' => 'From Webhook']);

    $resp = $this->withHeaders([
    'X-Shopify-Hmac-Sha256' => hmac($payload)
    ])->post('/api/webhooks/shopify/products/update', [], [], [], [], $payload);

    $resp->assertOk();
    Queue::assertPushed(ProcessShopifyProductWebhook::class);
});