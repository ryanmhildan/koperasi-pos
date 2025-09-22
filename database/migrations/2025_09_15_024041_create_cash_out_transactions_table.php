<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cash_out_transactions', function (Blueprint $table) {
            $table->id('cashout_id');
            $table->foreignId('card_id')->constrained('user_credit_cards', 'card_id')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('cash_out_transactions');
    }
};
