<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cash_drawers', function (Blueprint $table) {
            $table->id('drawer_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->decimal('opening_balance', 15, 2);
            $table->decimal('closing_balance', 15, 2)->nullable();
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->decimal('total_cash_in', 15, 2)->default(0);
            $table->decimal('total_cash_out', 15, 2)->default(0);
            $table->date('shift_date');
            $table->time('shift_start');
            $table->time('shift_end')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('cash_drawers');
    }
};
