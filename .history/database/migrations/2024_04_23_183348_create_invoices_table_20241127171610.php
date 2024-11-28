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
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('serie',25);
            $table->date('date');
            $table->string('payment_method', 12);
            $table->string('payment_conditions', 1000);

            $table->string('folio', 40);
            $table->string('payment_terms', 1000);
            $table->string('way_to_pay', 5);
            
            $table->string('currency', 5);
            $table->decimal('change_type', total: 36, places: 6);
            $table->decimal('subtotal', total: 36, places: 6);
            $table->decimal('discount', total: 36, places: 6);
            $table->decimal('total', total: 36, places: 6);
            $table->string('export', 5);
            $table->string('invoice_type', 3);
            $table->foreignUuid('receiver_id')->references('id')->on('customers');
            $table->string('invoice_usage', 5);
            $table->string('creation_date', 19);
            $table->string('timbre_date', 19);
            $table->string('uuid', 50);
            $table->longText('cfdi_seal');
            $table->longText('sat_seal');
           $table->enum('type_receipt', ['PDF', 'XML']);

            $table->string('supplier_rfc', 13);
            $table->enum('status', ["vigente", "en proceso", "cancelado", "rechazo de cancelacion"])->default('0');
            $table->string('type_relation',5)->nullable();
            $table->foreignUuid('uuid_company')
                  ->references('id')
                  ->on('companies')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
