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
        Schema::create('expenses', function (Blueprint $table) {

            $table->id();
            $table->string('product_name');
            $table->decimal('amount', 9, 2);
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('restrict');       // 👈 nullable + restrict
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onDelete('restrict'); // 👈 nullable + restrict
            $table->date('expense_date');
            $table->text('comments')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

public function down(): void
{
    Schema::table('expenses', function (Blueprint $table) {
        $table->dropForeign(['payment_method_id']);
        $table->dropForeign(['category_id']);
        $table->dropForeign(['user_id']);
    });
    
    Schema::dropIfExists('expenses');
}
};
