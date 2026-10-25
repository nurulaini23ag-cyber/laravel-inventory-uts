<?php


namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cost = fake()->randomFloat(2, 10000, 300000);
        $selling = round($cost * 1.25, 2);

        return [
            'category_id'    => Category::inRandomOrder()->value('id') ?? Category::factory(),
            'sku'            => fake()->unique()->bothify('SKU-####'),
            'name'           => fake()->words(3, true),
            'purchase_price' => $cost,
            'selling_price'  => $selling,
            'stock'          => fake()->numberBetween(0, 100),
            'minimum_stock'  => fake()->numberBetween(5, 15),
        ];
    }
}
