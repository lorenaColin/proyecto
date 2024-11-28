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
            database/migrations/2023_04_23_233211_create_companies_table.php
            // $table->string('street', 150)->nullable();
            // $table->string('ext_num', 80)->nullable();
            // $table->string('int_num', 80)->nullable();
            $table->string('municipality', 100);
            $table->string('colony', 100)->nullable();
            $table->string('locality',100)->nullable();
            $table->string('cp', 12);
            $table->string('rfc', 13);
            $table->string('regime', 5);
            $table->string('email', 75);
            $table->integer('estatus');
            $table->string('country', 5);
            $table->string('state', 5);
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
        Schema::dropIfExists('customers');
    }
};
