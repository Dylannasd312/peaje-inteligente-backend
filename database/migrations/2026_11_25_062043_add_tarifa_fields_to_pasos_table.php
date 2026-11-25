<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pasos', function (Blueprint $table) {
            $table->foreignId('tarifa_id')
                ->nullable()
                ->after('caseta_id')
                ->constrained('tarifas')
                ->restrictOnDelete();

            $table->decimal('monto_tarifa', 10, 2)
                ->nullable()
                ->after('tarifa_id');
        });
    }

    public function down(): void
    {
        Schema::table('pasos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tarifa_id');
            $table->dropColumn('monto_tarifa');
        });
    }
};
