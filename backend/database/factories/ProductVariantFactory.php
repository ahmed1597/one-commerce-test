<?php
namespace Database\Factories;


use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;


class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;
    public function definition(): array
    {
        return [
            'shopify_id' => 'v-'.fake()->unique()->numberBetween(1,999999),
            'sku' => 'SKU-'.fake()->bothify('????-#####'),
            'price' => fake()->randomFloat(2, 5, 200),
            'compare_at_price' => null,
            'position' => 1,
            'option1' => 'Default',
            'option2' => null,
            'option3' => null,
            'taxable' => true,
            'requires_shipping' => true,
            'inventory_quantity' => fake()->numberBetween(0, 50),
        ];
    }
}