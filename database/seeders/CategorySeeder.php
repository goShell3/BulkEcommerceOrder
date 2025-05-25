<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Create some predefined categories
        $categories = [
            [
                'name' => 'Running Shoes',
                'slug' => 'running-shoes',
                'description' => 'Professional running shoes for all types of runners',
            ],
            [
                'name' => 'Sports Apparel',
                'slug' => 'sports-apparel',
                'description' => 'High-quality sports clothing and accessories',
            ],
            [
                'name' => 'Training Equipment',
                'slug' => 'training-equipment',
                'description' => 'Essential equipment for your training needs',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create additional random categories
        Category::factory(5)->create();
    }
} 