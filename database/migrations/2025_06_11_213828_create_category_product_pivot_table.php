<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
        {
            Schema::create('category_product', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id');
                $table->unsignedBigInteger('product_id');
                
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                
                $table->primary(['category_id', 'product_id']);
            });
        }

    public function down()
        {
            Schema::dropIfExists('category_product');
        }
};
