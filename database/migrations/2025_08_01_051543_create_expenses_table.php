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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 9, 2);
            $table->date('expense_date');
            $table->string('description');
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->onDelete('cascade'); 
            $table->foreignId('installment_payment_id')->nullable()->constrained('installment_payments')->onDelete('cascade'); 
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onDelete('cascade'); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
