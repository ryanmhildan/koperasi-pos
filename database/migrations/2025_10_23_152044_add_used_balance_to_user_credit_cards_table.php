<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_credit_cards', function (Blueprint $table) {
            $table->decimal('used_balance', 15, 2)->default(0)->after('credit_limit');
        });

        // Migrate existing data
        DB::statement('UPDATE user_credit_cards SET used_balance = current_balance');
        DB::statement('UPDATE user_credit_cards SET current_balance = credit_limit - used_balance');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the data migration
        DB::statement('UPDATE user_credit_cards SET current_balance = used_balance');

        Schema::table('user_credit_cards', function (Blueprint $table) {
            $table->dropColumn('used_balance');
        });
    }
};