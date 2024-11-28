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
        Schema::create('gateways', function (Blueprint $table) {
            $table->id();
            $table->string('folios', 40);
            $table->string('gift_stamps', 40);
            $table->string('id_operation', 36)->nullable();
            $table->string('id_open_pay', 40);
            $table->enum('status', ['P', 'Inactivo']);

            $table->foreignUuid('uuid_company')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('id_sale')
                ->references('id')
                ->on('sales')
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
        Schema::dropIfExists('gateways');
    }
};
