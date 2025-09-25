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
        Schema::table('sales_transaction_details', function (Blueprint $table) {
            $table->decimal('average_price', 15, 2)->nullable()->after('selling_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_transaction_details', function (Blueprint $table) {
            $table->dropColumn('average_price');
        });
    }
};
