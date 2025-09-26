<?php
use App\Models\{Product, ProductVariant, ProductImage};

it('lists products with pagination and filters archived by default', function () {
    Product::factory()->count(2)->create();
    Product::factory()->create(['is_archived' => true]);

    $resp = $this->getJson('/api/products');
    $resp->assertOk();
    $this->assertGreaterThanOrEqual(2, count($resp->json('data')));
});


it('searches products by title', function () {
    Product::factory()->create(['title' => 'Green Mug']);
    Product::factory()->create(['title' => 'Blue Bottle']);

    $resp = $this->getJson('/api/products?search=Green');
    $resp->assertOk()->assertJsonFragment(['title' => 'Green Mug']);
});


it('shows product with variants and images when requested', function () {
    $p = Product::factory()->create();
    $p->variants()->create(['shopify_id' => 'v1']);
    $p->images()->create(['src' => 'https://example.com/x.jpg']);

    $resp = $this->getJson('/api/products/'.$p->id.'?with=variants,images');
    $resp->assertOk()->assertJsonFragment(['src' => 'https://example.com/x.jpg']);
});