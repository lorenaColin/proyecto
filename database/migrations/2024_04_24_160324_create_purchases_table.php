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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->decimal('cost_tones', total:36, places:6);
            $table->decimal('price_tones', total:36, places:6);
            $table->decimal('ganancia', total:36, places:6);
            $table->integer('tones_adq');
            $table->integer('estatus');

            $table->unsignedBigInteger('pac_id');

            $table->foreign('pac_id')
                ->references('id')
                ->on('pacs')
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
        Schema::dropIfExists('purchases');
    }
};
