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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['T', 'D', 'PE']);
            $table->string('description', 255);
            $table->integer('tones');
            $table->decimal('amount', total:30, places: 6);
            $table->date('date');
            $table->integer('id_usr');

            $table->foreignUuid('customer_id')
            ->references('id')
            ->on('customers')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            // $table->unsignedBigInteger('customer_id');

            // $table->foreign('customer_id')
            //     ->references('id')
            //     ->on('customers')
            //     ->onDelete('cascade')
            //     ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
