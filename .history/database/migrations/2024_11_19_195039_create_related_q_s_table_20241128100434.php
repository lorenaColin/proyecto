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
        Schema::create('relatedsQ', function (Blueprint $table) {
            $table->id();
            // $table->string('type_relation',5);
            $table->string('uuid',50);
            $table->timestamps();
            //llave foranea
            $table->unsignedBigInteger('quotation_id');
            $table->foreign('quotation_id')
                  ->references('id')
                  ->on('quotations')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relatedsQ');
    }
};
