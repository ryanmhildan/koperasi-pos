<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE sales_transactions DROP CONSTRAINT sales_transactions_payment_method_check');
        DB::statement("ALTER TABLE sales_transactions ADD CONSTRAINT sales_transactions_payment_method_check CHECK (payment_method IN ('cash', 'wallet'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE sales_transactions DROP CONSTRAINT sales_transactions_payment_method_check');
        DB::statement("ALTER TABLE sales_transactions ADD CONSTRAINT sales_transactions_payment_method_check CHECK (payment_method IN ('cash', 'credit_card'))");
    }
};
