<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grn_details', function (Blueprint $table) {
            $table->id('grn_detail_id');
            $table->foreignId('grn_id')->constrained('good_receipt_notes', 'grn_id')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products', 'product_id');
            $table->foreignId('location_id')->constrained('locations', 'location_id');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('grn_details');
    }
};
