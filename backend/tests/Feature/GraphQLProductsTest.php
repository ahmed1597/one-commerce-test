<?php
use App\Models\Product;

it('queries products via graphql', function () {
    $p = Product::factory()->create(['title' => 'GraphQL Mug']);
    $p->variants()->create(['shopify_id' => 'v1', 'price' => 12.34]);


    $query = '{ products { id title variants { price } } }';
    $resp = $this->postJson('/graphql', ['query' => $query]);
    $resp->assertOk()->assertJsonPath('data.products.0.title', 'GraphQL Mug');
});

it('queries single product by id via graphql', function () {
    $p = Product::factory()->create(['title' => 'Single Item']);
    $query = 'query($id: ID!){ product(id:$id){ id title } }';
    $resp = $this->postJson('/graphql', ['query' => $query, 'variables' => ['id' => $p->id]]);
    $resp->assertOk()->assertJsonPath('data.product.title', 'Single Item');
});