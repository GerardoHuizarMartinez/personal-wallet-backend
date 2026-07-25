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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string("name", 50);
            $table->string("first_last_name", 50);
            $table->string("second_last_name", 50)->nullable();
            $table->date("birthday")->nullable();
            $table->enum("gender", ['Male', 'Female'])->default('Male');
            $table->string('email')->unique();
            $table->string('password');
            $table->string("telephone", 20)->nullable();
            $table->string("cellphone", 20)->nullable();
            $table->string("country", 40)->nullable();
            $table->unsignedBigInteger("colony_id");
            $table->string("street", 70)->nullable();
            $table->string("no_ext", 20)->nullable();
            $table->string("no_int", 20)->nullable();
            $table->enum("status", ['Active', 'Inactive'])->default('Active');
            $table->text("url_image")->nullable();
            $table->foreign("colony_id")->references("id")->on("colonies")->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {

        Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['colony_id']);
    });
    
        Schema::dropIfExists('users');
    }
};
