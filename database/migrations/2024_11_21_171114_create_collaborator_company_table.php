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
        Schema::create('collaborator_company', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('collaborator_id');
            $table->foreign('collaborator_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade') 
                ->onUpdate('cascade');
        
            $table->uuid('company_id');
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade') 
                ->onUpdate('cascade');
            $table->enum('permiso',['Lectura','Escritura']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaborator_company');
    }
};
