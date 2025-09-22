<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock', function (Blueprint $table) {
            $table->id('stock_id');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations', 'location_id')->onDelete('cascade');
            $table->integer('current_stock')->default(0);
            $table->date('last_updated');
            $table->timestamps();
            
            $table->unique(['product_id', 'location_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('stock');
    }
};
