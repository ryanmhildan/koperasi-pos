<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->string('transaction_number')->unique();
            $table->foreignId('user_id')->constrained('users', 'user_id'); // customer
            $table->foreignId('cashier_id')->constrained('users', 'user_id'); // cashier
            $table->foreignId('drawer_id')->constrained('cash_drawers', 'drawer_id');
            $table->foreignId('card_id')->nullable()->constrained('user_credit_cards', 'card_id');
            $table->date('transaction_date');
            $table->time('transaction_time');
            $table->decimal('sub_total', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->enum('payment_method', ['cash', 'credit_card']);
            $table->enum('status', ['completed', 'void'])->default('completed');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('sales_transactions');
    }
};
