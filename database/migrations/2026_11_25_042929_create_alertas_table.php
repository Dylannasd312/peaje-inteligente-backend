
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paso_id')
                ->nullable()
                ->constrained('pasos');

            $table->string('tipo');
            $table->text('descripcion');

            $table->string('estado')->default('PENDIENTE');

            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
