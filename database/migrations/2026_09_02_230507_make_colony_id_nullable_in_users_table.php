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
        // No colonies picker exists in the app yet, so users can't always
        // supply one — relax the NOT NULL constraint (raw SQL: no doctrine/dbal
        // dependency installed for the ->nullable()->change() helper).
        DB::statement('ALTER TABLE users MODIFY colony_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE users MODIFY colony_id BIGINT UNSIGNED NOT NULL');
    }
};
