<?php
namespace App\Services\Shopify;

use App\Models\{Product, ProductVariant, ProductImage};
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class SyncProductsAction
{
    public function __construct(private ShopifyClient $client) {}

    public function handle(): array
    {
        $lastSync = Cache::get('shopify_last_sync_at');
        $incoming = $this->client->fetchProducts($lastSync);
        $count = 0;


        foreach ($incoming as $p) {
            DB::transaction(function () use ($p, &$count) {
                $product = Product::updateOrCreate(
                    ['shopify_id' => (string) $p['id']],
                    [
                        'title' => $p['title'] ?? 'Untitled',
                        'body_html' => $p['body_html'] ?? null,
                        'handle' => $p['handle'] ?? null,
                        'vendor' => $p['vendor'] ?? null,
                        'product_type' => $p['product_type'] ?? null,
                        'updated_at_shopify' => isset($p['updated_at']) ? Carbon::parse($p['updated_at']) : now(),
                        'is_archived' => ($p['status'] ?? 'active') !== 'active',
                    ]
                );

                $variants = $p['variants'] ?? [];
                $rows = [];
                foreach ($variants as $v) {
                    $rows[] = [
                        'product_id' => $product->id,
                        'shopify_id' => (string) $v['id'],
                        'sku' => $v['sku'] ?? null,
                        'price' => $v['price'] ?? null,
                        'compare_at_price' => $v['compare_at_price'] ?? null,
                        'position' => $v['position'] ?? 1,
                        'option1' => $v['option1'] ?? null,
                        'option2' => $v['option2'] ?? null,
                        'option3' => $v['option3'] ?? null,
                        'taxable' => (bool) ($v['taxable'] ?? true),
                        'requires_shipping' => (bool) ($v['requires_shipping'] ?? true),
                        'inventory_quantity' => $v['inventory_quantity'] ?? null,
                        'created_at' => now(), 'updated_at' => now(),
                    ];
                }
                if ($rows) {
                    ProductVariant::upsert($rows, ['shopify_id'], [
                        'sku','price','compare_at_price','position','option1','option2','option3','taxable','requires_shipping','inventory_quantity','updated_at','product_id'
                    ]);
                }

                $images = $p['images'] ?? [];
                if ($images) {
                    $product->images()->delete();
                    $imgRows = [];
                    foreach ($images as $i) {
                        $imgRows[] = [
                        'product_id' => $product->id,
                        'shopify_id' => isset($i['id']) ? (string) $i['id'] : null,
                        'src' => $i['src'],
                        'position' => $i['position'] ?? 1,
                        'created_at' => now(), 'updated_at' => now(),
                        ];
                    }
                    ProductImage::insert($imgRows);
                }
                $count++;
            }); 
        }

        Cache::put('shopify_last_sync_at', now()->toIso8601String(), 3600);
        return ['count' => $count];
    }
}