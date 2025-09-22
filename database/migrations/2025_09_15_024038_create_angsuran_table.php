<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('angsuran', function (Blueprint $table) {
            $table->id('angsuran_id');
            $table->foreignId('pinjaman_id')->constrained('pinjaman', 'pinjaman_id')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->decimal('denda', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('angsuran');
    }
};
