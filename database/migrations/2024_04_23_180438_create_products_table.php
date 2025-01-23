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
            $table->uuid('id')->primary();
            $table->string('product_key', length: 8);
            $table->string('unit', 5);
            // $table->string('description',1000);
            $table->string('unit_description', 1000);
            $table->decimal('unit_price', total: 30, places: 6);           
            $table->decimal('quantity', total: 30, places: 6);
            $table->boolean('status');
           $table->integer('identifier_number');
           $table->string('internal_key',  20); 
           $table->string('description',length: 1000); 
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
