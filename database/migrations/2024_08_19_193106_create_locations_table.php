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
        Schema::create('location_c_p', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('rfc',13);
            $table->string('id_ubication',8)->nullable();
            $table->string('name',254)->nullable();
            $table->string('num_reg_trib',40)->nullable();
            $table->string('residence_fiscal', 5)->nullable();
            $table->string('country',20)->nullable();
            $table->string('cp',15)->nullable();
            $table->string('state',30)->nullable();
            $table->string('municipality',30)->nullable();
            $table->string('locality',30)->nullable();
            $table->string('colony', 150)->nullable();
            $table->string('street', 150)->nullable();
            $table->string('ext_num', 80)->nullable();
            $table->string('int_num', 80)->nullable();
            $table->string('references', 80)->nullable();
            $table->string('tipo', 15);
            $table->foreignUuid('customer_id')
            ->references('id')
            ->on('customers')
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
        Schema::dropIfExists('locations');
    }
};
