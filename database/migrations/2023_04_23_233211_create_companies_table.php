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
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->longText('address')->nullable();
            $table->string('colony', 100)->nullable();
            $table->string('municipality', 3);
            $table->string('cp', 5);
            $table->string('curp', 18)->nullable();
            $table->enum('status', ['Activo', 'Inactivo'])->default('Activo');
            $table->string('rfc', 13);
            $table->string('state', 3);
            $table->string('locality', 3)->nullable();
            $table->string('regime', 3);
            $table->string('employee_registration', 20)->nullable();
            $table->enum('type', ['P', 'H'])->default('H');
            $table->string('email', 75);
            $table->string('phone', 13);
            $table->string('logo', 100)->nullable();
            $table->integer('id_usr_create')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
