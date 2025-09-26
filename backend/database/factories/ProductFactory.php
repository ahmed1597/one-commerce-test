<?php
namespace Database\Factories;


use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;


class ProductFactory extends Factory
{
    protected $model = Product::class;
    public function definition(): array
        {
        return [
            'shopify_id' => 'seed-'.fake()->unique()->numberBetween(1, 999999),
            'title' => fake()->words(2, true),
            'body_html' => '<p>'.fake()->sentence().'</p>',
            'handle' => fake()->slug(),
            'vendor' => fake()->company(),
            'product_type' => 'Test',
            'updated_at_shopify' => now(),
            'is_archived' => false,
        ];
    }
}