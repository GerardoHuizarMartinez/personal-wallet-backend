<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->decimal('total_price', 9, 2);
            $table->foreignId('user_id')->constrained('users','id');
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('financing_type_id')->constrained('financing_types')->default(0);
            $table->string('financing_method');     
            $table->foreignId('payment_method_id')->constrained('payment_methods')->default(1);
            $table->string('comments')->nullable();
            $table->date('purchase_date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
