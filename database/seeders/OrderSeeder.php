<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\Product;
use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\Category;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 orders with various statuses
        Order::factory()
            ->count(10)
            ->pending()
            ->create();

        Order::factory()
            ->count(10)
            ->processing()
            ->create();

        Order::factory()
            ->count(10)
            ->shipped()
            ->create();

        Order::factory()
            ->count(10)
            ->delivered()
            ->create();

        Order::factory()
            ->count(10)
            ->cancelled()
            ->create();

        // Create some return requests for delivered orders
        Order::where('status', 'delivered')
            ->get()
            ->each(function ($order) {
                // 30% chance of having a return request
                if (rand(1, 100) <= 30) {
                    ReturnRequest::factory()
                        ->count(1)
                        ->create([
                            'order_id' => $order->id,
                        ]);
                }
            });

        // Create some recent orders with return requests
        Order::factory()
            ->count(5)
            ->delivered()
            ->recent()
            ->create()
            ->each(function ($order) {
                ReturnRequest::factory()
                    ->count(1)
                    ->pending()
                    ->create([
                        'order_id' => $order->id,
                    ]);
            });

        // Create some orders with damaged product returns
        Order::factory()
            ->count(5)
            ->delivered()
            ->create()
            ->each(function ($order) {
                ReturnRequest::factory()
                    ->count(1)
                    ->damaged()
                    ->create([
                        'order_id' => $order->id,
                    ]);
            });

        // Create some orders with wrong item returns
        Order::factory()
            ->count(5)
            ->delivered()
            ->create()
            ->each(function ($order) {
                ReturnRequest::factory()
                    ->count(1)
                    ->wrongItem()
                    ->create([
                        'order_id' => $order->id,
                    ]);
            });

        // Create 10 orders with random items
        Order::factory(10)->create()->each(function ($order) {
            // Add 1-5 random products to each order
            $products = Product::inRandomOrder()->take(rand(1, 5))->get();
            
            foreach ($products as $product) {
                $quantity = rand(1, 3);
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);
            }

            // Update order total
            $order->update([
                'total' => $order->items->sum(function ($item) {
                    return $item->price * $item->quantity;
                })
            ]);
        });
    }
} 