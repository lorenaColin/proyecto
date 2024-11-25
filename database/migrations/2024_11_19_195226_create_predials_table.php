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
        Schema::create('predials', function (Blueprint $table) {
            $table->id();
            $table->string('account',50);

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
        Schema::dropIfExists('prediales');
    }
};
