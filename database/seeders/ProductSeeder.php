<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Get brand IDs
        $nikeBrandId = Brand::where('slug', 'nike')->first()->id;
        $adidasBrandId = Brand::where('slug', 'adidas')->first()->id;

        // Create some predefined products
        $products = [
            [
                'brand_id' => $nikeBrandId,
                'name' => 'Nike Air Max',
                'slug' => 'nike-air-max',
                'description' => 'Classic running shoes with air cushioning',
                'price' => 129.99,
                'stock' => 50,
                'is_active' => true,
            ],
            [
                'brand_id' => $adidasBrandId,
                'name' => 'Adidas Ultraboost',
                'slug' => 'adidas-ultraboost',
                'description' => 'Premium running shoes with responsive boost technology',
                'price' => 159.99,
                'stock' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Create additional random products
        Product::factory(20)->create()->each(function ($product) {
            // Attach 1-3 random categories to each product
            $categories = Category::inRandomOrder()->take(rand(1, 3))->get();
            $product->categories()->attach($categories);
        });
    }
} 