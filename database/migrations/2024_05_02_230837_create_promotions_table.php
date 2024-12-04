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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->date('date_init');
            $table->date('date_end');
            $table->integer('new_quantity')->nullable();
            $table->decimal('new_percentaje', total: 5, places: 2)->nullable();
            $table->integer('current_quantity')->nullable();
            $table->decimal('current_percentaje', total: 5, places: 2)->nullable();
            $table->enum('type', ['R', 'A']);
            $table->enum('status', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
