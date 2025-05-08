<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // agregamos la columna justo después de 'date' (o donde prefieras)
            $table->string('xml_filename', 255)
                  ->nullable()
                  ->after('date');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('xml_filename');
        });
    }
};
