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
        Schema::create('company_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tones_incluide');
            $table->integer('pac_id');
            $table->date('fechaco');
            $table->date('fechaven');
            $table->enum('sta_prod', ['P', 'T'])->default('P');
            $table->string('certificate', 255)->nullable();
            $table->string('private_key', 255)->nullable();
            $table->longText('password_key')->nullable();
            $table->longText('contcert')->nullable();
            $table->date('expiration_date_cert')->nullable();
            $table->date('start_date_cert')->nullable();
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
        Schema::dropIfExists('customer_details');
    }
};
