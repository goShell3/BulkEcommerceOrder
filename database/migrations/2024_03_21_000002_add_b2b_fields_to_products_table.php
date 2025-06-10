<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_b2b')->default(false)->after('is_active');
            $table->decimal('b2b_price', 10, 2)->nullable()->after('price');
            $table->integer('min_order_quantity')->default(1)->after('b2b_price');
            $table->integer('max_order_quantity')->nullable()->after('min_order_quantity');
            $table->json('bulk_pricing')->nullable()->after('max_order_quantity');
            $table->json('specifications')->nullable()->after('bulk_pricing');
            $table->boolean('requires_approval')->default(false)->after('specifications');
            $table->text('approval_notes')->nullable()->after('requires_approval');
            $table->boolean('is_featured_b2b')->default(false)->after('approval_notes');
            $table->timestamp('featured_until')->nullable()->after('is_featured_b2b');
        });

        // Create bulk pricing tiers table
        Schema::create('bulk_pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('min_quantity');
            $table->integer('max_quantity')->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_pricing_tiers');
        
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'is_b2b',
                'b2b_price',
                'min_order_quantity',
                'max_order_quantity',
                'bulk_pricing',
                'specifications',
                'requires_approval',
                'approval_notes',
                'is_featured_b2b',
                'featured_until'
            ]);
        });
    }
}; 