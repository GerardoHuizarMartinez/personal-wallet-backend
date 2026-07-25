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

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('label', 30)->nullable();
            $table->enum('type', ['income', 'expense'])->default('expense');
            $table->string('icon')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('categories');
        Schema::enableForeignKeyConstraints();
    }
};
