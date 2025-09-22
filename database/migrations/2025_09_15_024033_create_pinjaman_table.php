<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->id('pinjaman_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->decimal('loan_amount', 15, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->integer('tenor_months');
            $table->enum('loan_type', ['regular', 'emergency', 'business']);
            $table->string('loan_purpose');
            $table->date('loan_date');
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->boolean('is_blocked')->default(false);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->decimal('remaining_balance', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pinjaman');
    }
};
