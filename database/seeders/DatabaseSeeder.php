<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Truncate all tables in reverse order of dependencies
        DB::table('category_product')->truncate();
        DB::table('order_items')->truncate();
        DB::table('return_requests')->truncate();
        DB::table('orders')->truncate();
        DB::table('products')->truncate();
        DB::table('categories')->truncate();
        DB::table('brands')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Run seeders in order of dependencies
        $this->call([
            BrandSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
