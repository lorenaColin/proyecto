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
        Schema::create('quotation_details', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_product', total:30, places:6);
            // $table->decimal('amount', total: 8, places: 2);
            $table->string('product_service_code', 8);
            $table->string('description', 1000);
            $table->decimal('quantity', total: 36, places: 2);
            $table->decimal('unit_value', total: 30, places: 6);
            $table->string('unit_key', 15);
            $table->string('identification_number', 100);
            $table->string('tax_object', 2);
            $table->decimal('discount', total: 30, places: 6);
            $table->decimal('discount_percentage', total: 36, places: 6);
            $table->decimal('base', total: 36, places: 6);
            
            $table->unsignedBigInteger('quotation_id');
            $table->foreign('quotation_id')
                  ->references('id')
                  ->on('quotations')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_details');
    }
};
