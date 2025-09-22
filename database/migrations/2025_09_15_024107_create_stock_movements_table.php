<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id('movement_id');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations', 'location_id')->onDelete('cascade');
            $table->enum('movement_type', ['in', 'out', 'adjustment']);
            $table->integer('quantity');
            $table->decimal('average_price', 15, 2)->nullable();
            $table->decimal('total_price', 15, 2)->nullable();
            $table->string('reference_type'); // purchase, sale, adjustment
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->date('movement_date');
            $table->string('notes')->nullable();
            $table->foreignId('created_by')->constrained('users', 'user_id');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('stock_movements');
    }
};
