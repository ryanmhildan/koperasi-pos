<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cash_out_transactions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id')->after('cashout_id');
            $table->string('status')->default('pending')->after('notes'); // pending, approved, rejected
            $table->foreignId('card_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_out_transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->dropColumn('status');
            $table->foreignId('card_id')->nullable(false)->change();
        });
    }
};
