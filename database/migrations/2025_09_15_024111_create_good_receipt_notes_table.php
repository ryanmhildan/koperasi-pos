<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('good_receipt_notes', function (Blueprint $table) {
            $table->id('grn_id');
            $table->foreignId('location_id')->constrained('locations', 'location_id');
            $table->date('receipt_date');
            $table->string('reference_number')->unique();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('good_receipt_notes');
    }
};
