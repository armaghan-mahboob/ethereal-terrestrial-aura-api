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
        Schema::create('products', function (Blueprint $table) {
            // Keep Base44-style string IDs so existing frontend links remain valid.
            $table->string('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('subcategory')->nullable();
            $table->decimal('price', 10, 2);
            $table->json('images')->nullable();
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->boolean('in_stock')->default(true);
            $table->boolean('is_new_arrival')->default(false);
            $table->json('sizes')->nullable();
            $table->json('colors')->nullable();
            $table->boolean('featured')->default(false);
            $table->integer('sort_order')->nullable();
            $table->timestamps();

            $table->index(['category', 'subcategory']);
            $table->index(['featured', 'is_new_arrival']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
