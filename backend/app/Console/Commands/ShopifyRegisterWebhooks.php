<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class ShopifyRegisterWebhooks extends Command
{
    protected $signature = 'shopify:webhooks:register-graphql';
    protected $description = 'Register product webhooks via Shopify GraphQL Admin API';

    public function handle(): int
    {
        $shop    = config('services.shopify.shop');
        $token   = config('services.shopify.token');
        $version = config('services.shopify.version', '2025-07');

        $baseUrl = rtrim(config('app.url'), '/');
        if (!str_starts_with($baseUrl, 'https://')) {
            $this->error("APP_URL must be https (current: {$baseUrl})");
            return self::FAILURE;
        }

        $client = new Client([
            'base_uri' => "https://{$shop}/admin/api/{$version}/graphql.json",
            'headers'  => [
                'X-Shopify-Access-Token' => $token,
                'Content-Type'           => 'application/json',
                'Accept'                 => 'application/json',
            ],
            'http_errors' => false,
            'timeout'     => 30,
        ]);

        $query = <<<'GQL'
        query Webhooks($first: Int!) {
          webhookSubscriptions(first: $first) {
            edges {
              node {
                id
                topic
                endpoint {
                  __typename
                  ... on WebhookHttpEndpoint {
                    callbackUrl
                  }
                }
              }
            }
          }
        }
        GQL;

        $existing = $this->graphql($client, $query, ['first' => 100]);
        $existingList = collect(data_get($existing, 'data.webhookSubscriptions.edges', []))
            ->map(fn($e) => [
                'id'    => data_get($e, 'node.id'),
                'topic' => data_get($e, 'node.topic'),
                'url'   => data_get($e, 'node.endpoint.callbackUrl'),
            ]);

        $callbacks = [
            'PRODUCTS_CREATE' => "{$baseUrl}/api/webhooks/shopify/products/create",
            'PRODUCTS_UPDATE' => "{$baseUrl}/api/webhooks/shopify/products/update",
            'PRODUCTS_DELETE' => "{$baseUrl}/api/webhooks/shopify/products/delete",
        ];

        $mutation = <<<'GQL'
        mutation CreateWebhook($topic: WebhookSubscriptionTopic!, $callbackUrl: URL!) {
          webhookSubscriptionCreate(
            topic: $topic,
            webhookSubscription: { callbackUrl: $callbackUrl, format: JSON }
          ) {
            userErrors { field message }
            webhookSubscription {
              id
              topic
              endpoint {
                __typename
                ... on WebhookHttpEndpoint { callbackUrl }
              }
            }
          }
        }
        GQL;

        foreach ($callbacks as $topic => $url) {
            $already = $existingList->first(fn($w) => $w['topic'] === $topic && $w['url'] === $url);
            if ($already) {
                $this->info("✓ Exists: {$topic} -> {$url}");
                continue;
            }

            $this->info("Creating: {$topic} -> {$url}");
            $resp = $this->graphql($client, $mutation, ['topic' => $topic, 'callbackUrl' => $url]);

            $errors = data_get($resp, 'data.webhookSubscriptionCreate.userErrors', []);
            if (!empty($errors)) {
                foreach ($errors as $err) {
                    $this->error("UserError: ".json_encode($err));
                }
                return self::FAILURE;
            }

            $created = data_get($resp, 'data.webhookSubscriptionCreate.webhookSubscription');
            if ($created) {
                $this->info("✓ Created: ".data_get($created, 'topic').' -> '.data_get($created, 'endpoint.callbackUrl'));
            } else {
                $this->error('Unknown error creating webhook (no subscription returned). Response: '.json_encode($resp));
                return self::FAILURE;
            }
        }

        $this->info('Done.');
        return self::SUCCESS;
    }

    private function graphql(Client $client, string $query, array $variables = []): array
    {
        $res = $client->post('', ['body' => json_encode(['query' => $query, 'variables' => $variables])]);
        $code = $res->getStatusCode();
        $body = (string) $res->getBody();

        $this->line("HTTP {$code}");
        if ($code >= 400) {
            $this->error("GraphQL error: {$body}");
        }

        return json_decode($body, true) ?? [];
    }
}
