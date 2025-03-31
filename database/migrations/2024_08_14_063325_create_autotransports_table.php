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
        Schema::create('autotransports', function (Blueprint $table) {
            $table->id();
            $table->string('configVehicular', 11);
            $table->decimal('pesoBrutoVehicular', total: 12, places: 2); // Revisar documentacion para la cantidad total
            $table->string('placaVM', 7);
            $table->integer('anioModeloVM');
            $table->string('permSCT', 6);
            $table->string('numPermisoSCT', 50);
            $table->string('aseguraRespCivil', 50);
            $table->string('polizaRespCivil', 30);
            $table->string('tipoRemolque');
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
        Schema::dropIfExists('autotransports');
    }
};
