
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehiculo_id')
                ->nullable()
                ->constrained('vehiculos');

            $table->foreignId('caseta_id')
                ->constrained('casetas');

            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('users');

            $table->string('placa_detectada', 20)->nullable();
            $table->string('categoria_detectada', 30)->nullable();

            $table->integer('ejes')->nullable();

            $table->decimal('confianza_placa', 6, 4)->nullable();
            $table->decimal('confianza_categoria', 6, 4)->nullable();

            $table->string('imagen_evidencia')->nullable();

            $table->decimal('latencia_ms', 10, 2)->nullable();

            $table->timestamp('fecha_hora')->useCurrent();

            $table->string('estado')->default('PROCESADO');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasos');
    }
};
