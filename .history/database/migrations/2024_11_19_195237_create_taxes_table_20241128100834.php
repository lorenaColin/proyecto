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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('tax_type',45);
            $table->string('rate_quota',45);
            $table->decimal('rate_quota', total: 30, places: 6);

            $table->string('factor_type',45);
            $table->boolean('withheld');
            
            // $table->unsignedBigInteger('invoice_detail_id');
            // $table->foreign('invoice_detail_id')
            //     ->references('id')
            //     ->on('invoice_details')
            //     ->onDelete('cascade')
            //     ->onUpdate('cascade');

            $table->foreignId('invoice_detail_id')
                ->nullable()
                ->constrained('invoice_details')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('quotation_detail_id')
                ->nullable()
                ->constrained('quotation_details')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
