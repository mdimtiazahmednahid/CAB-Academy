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
        Schema::create('financial_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('campus_operations'); // faculty_honorarium, campus_operations, marketing_ads, it_hosting, materials_printing, corporate_tax, other
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->string('paid_to');
            $table->string('payment_method')->default('bank_transfer'); // cash, bank_transfer, bkash, cheque
            $table->string('reference_no')->nullable();
            $table->string('receipt_file')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_expenses');
    }
};
