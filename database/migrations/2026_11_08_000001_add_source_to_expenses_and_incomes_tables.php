<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Origen del registro: 'manual' (captura individual) o 'import' (carga masiva).
     * Los registros existentes quedan como 'manual'.
     */
    public function up(): void
    {
        foreach (['expenses', 'incomes'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('source', 20)->default('manual');
            });
        }
    }

    public function down(): void
    {
        foreach (['expenses', 'incomes'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('source');
            });
        }
    }
};
