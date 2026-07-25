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
        Schema::create('colonies', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('zipcode');
            $table->string('name', 100);
            $table->unsignedSmallInteger('settlement_type_code');
            $table->string('settlement_type', 500);
            $table->unsignedSmallInteger('city_code');
            $table->string('city', 100);
            $table->tinyInteger('state_code');
            $table->string('state', 50);
            $table->unsignedMediumInteger('office_code');
            $table->string('zone', 25);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colonies');
    }
};
