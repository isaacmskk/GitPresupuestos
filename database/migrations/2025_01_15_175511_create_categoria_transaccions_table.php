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
        Schema::create('categoria_transaccion', function (Blueprint $table) {
            $table->id(); // Identificador único para esta relación
            $table->unsignedBigInteger('categoria_id'); // Clave foránea para categorías
            $table->unsignedBigInteger('transaccion_id'); // Clave foránea para transacciones
            $table->timestamps(); // created_at y updated_at

            // Definir las claves foráneas
            $table->foreign('categoria_id')
                  ->references('id')
                  ->on('categorias')
                  ->onDelete('cascade');

            $table->foreign('transaccion_id')
                  ->references('id')
                  ->on('transacciones')
                  ->onDelete('cascade');

            // Evitar duplicados en la relación
            $table->unique(['categoria_id', 'transaccion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria_transaccion');
    }
};
