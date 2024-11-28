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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('product_key', 8);
            $table->string('unit', 5);
            $table->longText('description');
            $table->string('unit_description', 20);
            
           $table->string('identifier_number', 100); 
            $table->enum('estatus', ['1', '0']);
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
        Schema::dropIfExists('products');
    }
};
