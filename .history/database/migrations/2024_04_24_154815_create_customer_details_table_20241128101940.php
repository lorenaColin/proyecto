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
        Schema::create('customer_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tones_incluide');
            $table->integer('pac_id');
            $table->string('certificate', 255);
            $table->string('private_key', 255);
            $table->string('password_key', 255);
            $table->longText('contcert');
            $table->date('expiration_date_cert');
            $table->string('start_date_cert', 25);

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
        Schema::dropIfExists('customer_details');
    }
};
