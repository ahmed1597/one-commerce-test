<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Product;


class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $p = Product::factory()->create(['title' => 'Seeded Bottle']);
        $p->variants()->create([
        'shopify_id' => 'v-seed-1', 'sku' => 'SB-001', 'price' => 29.99,
        'position' => 1, 'option1' => 'Default', 'taxable' => true,
        'requires_shipping' => true, 'inventory_quantity' => 10
        ]);
        $p->images()->create(['shopify_id' => 'img-seed-1', 'src' => 'https://picsum.photos/200', 'position' => 1]);
    }
}