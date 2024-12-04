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
            $table->string('name', 255);
            $table->longText('address')->nullable();
            $table->string('municipality', 3);
            $table->string('colony', 100)->nullable();
            $table->string('locality',3)->nullable();
            $table->string('cp', 12);
            $table->string('rfc', 13);
            $table->string('regime', 3);
            $table->string('email', 75);
            $table->enum('status', ['Activo', 'Inactivo'])->default('Activo');
            $table->string('country', 3);
            $table->string('state', 3);
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
