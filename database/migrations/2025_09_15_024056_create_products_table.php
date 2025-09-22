<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_code')->unique();
            $table->string('barcode')->nullable();
            $table->string('product_name');
            $table->foreignId('category_id')->constrained('categories', 'category_id');
            $table->foreignId('unit_id')->constrained('units', 'unit_id');
            $table->decimal('selling_price', 15, 2);
            $table->boolean('is_stock_item')->default(true);
            $table->enum('product_type', ['retail', 'catering_package', 'service'])->default('retail');
            $table->integer('minimum_stock')->default(0);
            $table->boolean('track_expiry')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};
