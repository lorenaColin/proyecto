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
            $table->string('c_vehicle', 11);
            $table->integer('anio');
            $table->string('p_vehicle', 7);
            $table->decimal('w_gross', total: 12, places: 2); // Revisar documentacion para la cantidad total

            $table->foreignUuid('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('asegure', 11);
            $table->string('polize', 11);


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
