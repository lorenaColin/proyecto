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
        Schema::create('mercancias', function (Blueprint $table) {
            $table->id();
            $table->string('claveProdServCP');
            $table->string('descripcion');
            $table->string('claveUnidad');
            $table->string('unidad');
            $table->string('dimensiones')->nullable();
            $table->boolean('materialPeligroso')->nullable();
            $table->string('cveMaterialPeligroso')->nullable();
            $table->string('embalaje')->nullable();
            $table->string('descripEmbalaje')->nullable();
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
        Schema::dropIfExists('mercancias');
    }
};
