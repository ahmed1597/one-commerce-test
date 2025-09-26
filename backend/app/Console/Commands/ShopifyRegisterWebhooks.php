<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class ShopifyRegisterWebhooks extends Command
{
    protected $signature = 'shopify:webhooks:register';
    protected $description = 'Register product webhooks with Shopify';

    public function handle(): int
    {
        $shop = config('services.shopify.shop');
        $token = config('services.shopify.token');
        $version = config('services.shopify.version');
        $base = "https://{$shop}/admin/api/{$version}/webhooks.json";
        $client = new Client(['headers' => [
            'X-Shopify-Access-Token' => $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]]);

        $topics = [
            'products/create' => url('/api/webhooks/shopify/products/create'),
            'products/update' => url('/api/webhooks/shopify/products/update'),
            'products/delete' => url('/api/webhooks/shopify/products/delete'),
        ];

        foreach ($topics as $topic => $callback) {
            $this->info("Registering {$topic} -> {$callback}");
            $client->post($base, ['json' => [
                'webhook' => [ 'topic' => $topic, 'address' => $callback, 'format' => 'json' ]
            ]]);
        }

        $this->info('Done.');
        return self::SUCCESS;
    }
}