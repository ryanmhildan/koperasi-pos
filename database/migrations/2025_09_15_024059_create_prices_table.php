<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id('price_id');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations', 'location_id')->onDelete('cascade');
            $table->decimal('average_price', 15, 2);
            $table->timestamps();
            
            $table->unique(['product_id', 'location_id']);
        });
    }


    public function down(): void {
        Schema::dropIfExists('prices');
    }
};
