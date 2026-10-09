<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE sales_transactions DROP CONSTRAINT sales_transactions_status_check');
        DB::statement("ALTER TABLE sales_transactions ADD CONSTRAINT sales_transactions_status_check CHECK (status IN ('completed', 'void', 'voided'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE sales_transactions DROP CONSTRAINT sales_transactions_status_check');
        DB::statement("ALTER TABLE sales_transactions ADD CONSTRAINT sales_transactions_status_check CHECK (status IN ('completed', 'void'))");
    }
};
