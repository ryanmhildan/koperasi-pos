<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_transaction_details', function (Blueprint $table) {
            $table->id('detail_id');
            $table->foreignId('transaction_id')->constrained('sales_transactions', 'transaction_id')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products', 'product_id');
            $table->integer('quantity');
            $table->decimal('selling_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('sales_transaction_details');
    }
};
