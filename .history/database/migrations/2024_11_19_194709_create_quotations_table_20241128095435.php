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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('serie',25);
            $table->date('date');
            $table->string('way_to_pay', 12);
            $table->string('payment_terms', 1000);
            $table->decimal('subtotal', total: 30, places: 2);
            $table->decimal('discount', total: 30, places: 2);
            $table->string('currency', 5);
            $table->decimal('change_type', total: 36, places: 6);
            $table->decimal('total', total: 30, places: 2);
            $table->string('payment_method', 5);
            $table->string('receipt_type', 45);
            $table->string('export', 5);


            $table->string('folio', 40);
            
            
            // $table->string('quotation_type', 3);
            $table->foreignUuid('customer_id')->references('id')->on('customers');
            $table->string('quotation_usage', 5);
            // $table->string('creation_date', 19);
            // $table->string('timbre_date', 19);
            // $table->string('uuid', 50);
            // $table->longText('cfdi_seal');
            // $table->longText('sat_seal');
            $table->string('supplier_rfc', 13);
            $table->enum('status', ['0','1','2', '3'])->default('0');
            $table->foreignUuid('company_id')
                  ->references('id')
                  ->on('companies')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->string('type_relation',5)->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
