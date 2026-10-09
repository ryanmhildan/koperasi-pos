<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE pinjaman DROP CONSTRAINT pinjaman_status_check');
        DB::statement("ALTER TABLE pinjaman ADD CONSTRAINT pinjaman_status_check CHECK (status IN ('active', 'closed', 'pending'))");
        DB::statement("ALTER TABLE pinjaman ALTER COLUMN status SET DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE pinjaman DROP CONSTRAINT pinjaman_status_check');
        DB::statement("ALTER TABLE pinjaman ADD CONSTRAINT pinjaman_status_check CHECK (status IN ('active', 'closed'))");
        DB::statement("ALTER TABLE pinjaman ALTER COLUMN status SET DEFAULT 'active'");
    }
};
