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
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('rfc', 13);
            $table->string('name', 254);
            $table->string('cp', 5)->nullable();
            $table->string('residence', 3)->nullable();
            $table->string('num_reg_id_trib', 40)->nullable();
            $table->string('regime', 3);
            $table->longText('address')->nullable();            
            $table->string('email', 75)->nullable();
            $table->string('phone', 13)->nullable();
            $table->string('payment_form', 2);
            $table->string('payment_method', 3);
            $table->boolean('status');
            $table->foreignUuid('company_id')
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
        Schema::dropIfExists('customers');
    }
};
