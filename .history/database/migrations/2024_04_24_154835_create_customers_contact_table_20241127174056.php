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
        Schema::create('customer_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('email_contact', 75);
            $table->string('phone_office', 7)->nullable();
            $table->string('phone_movil', 75);
            $table->string('name_contact', 100);
            $table->longText('description')->nullable();

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
        Schema::dropIfExists('customer_contact');
    }
};
