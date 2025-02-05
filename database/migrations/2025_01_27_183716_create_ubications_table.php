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
        Schema::create('ubications', function (Blueprint $table) {
            $table->id();
            $table->string('rfc', 13);
            $table->string('idUbicacion')->nullable();
            $table->string('NombreRemitenteDestinatario', 254)->nullable();
            $table->string('numRegIdTrib', 40)->nullable();
            $table->string('residenciaFiscal')->nullable();
            $table->string('tipoUbicacion');
            $table->string('domicilio')->nullable();
            $table->string('pais')->nullable();
            $table->string('codigoPostal')->nullable();
            $table->string('estado',30)->nullable();
            $table->string('municipio')->nullable();
            $table->string('localidad')->nullable();
            $table->string('colonia')->nullable();
            $table->string('calle')->nullable();
            $table->string('numeroExterior')->nullable();
            $table->string('numeroInterior')->nullable();
            $table->string('referencia')->nullable();
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
        Schema::dropIfExists('ubications');
    }
};
