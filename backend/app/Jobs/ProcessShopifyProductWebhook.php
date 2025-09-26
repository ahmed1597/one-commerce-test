<?php
namespace App\Jobs;


use App\Models\{Product, ProductVariant, ProductImage};
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ProcessShopifyProductWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $event, public array $payload) {}

    public function handle(): void
    {
        $p = $this->payload;
        if ($this->event === 'delete') {
            $existing = Product::where('shopify_id', (string)($p['id'] ?? ''))->first();
            if ($existing) $existing->update(['is_archived' => true]);
            return;
        }

        DB::transaction(function () use ($p) {
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


            $rows = [];
            foreach ($p['variants'] ?? [] as $v) {
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


            if (!empty($p['images'])) {
                $product->images()->delete();
                $imgRows = [];
                foreach ($p['images'] as $i) {
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
        
        });
    }

}