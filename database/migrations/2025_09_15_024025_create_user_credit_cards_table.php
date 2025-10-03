<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_credit_cards', function (Blueprint $table) {
            $table->id('card_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('card_number');
            $table->decimal('credit_limit', 15, 2);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->decimal('cash_out_limit', 15, 2);
            $table->decimal('cash_out_used_this_month', 15, 2)->default(0);
            $table->string('expiry_date');
            $table->string('bank_name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('user_credit_cards');
    }
};
