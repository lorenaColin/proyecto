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
            // $table->string('street', 150)->nullable();
            // $table->string('ext_num', 80)->nullable();
            // $table->string('int_num', 80)->nullable();
            $table->string('colony', 100)->nullable();
            $table->string('municipality', 3);
            $table->string('cp', 5);
            $table->integer('estatus');
            $table->enum('type', ['P', 'H']);
            $table->string('rfc', 13);
            // $table->string('country', 100)->default('MEX');
            $table->string('state', 3);
            $table->string('locality',3)->nullable();
            $table->string('regime', 3);
            $table->string('employee_registration',100);
            $table->string('logo', 100)->nullable();
            $table->enum('type', ['P', 'H']);
            $table->integer('id_usr_create');
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
