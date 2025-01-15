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
        Schema::create('transacciones', function (Blueprint $table) {
            $table->id(); // Identificador único
            $table->string('descripcion'); // Descripción de la transacción
            $table->decimal('monto', 10, 2); // Monto de la transacción
            $table->unsignedBigInteger('categoria_id'); // Relación con la tabla de categorías
            $table->enum('tipo', ['ingreso', 'gasto']); // Tipo de transacción
            $table->timestamp('fecha')->useCurrent(); // Fecha de la transacción
            $table->foreign(columns: 'categoria_id')->references('id')->on(table: 'categorias')->onDelete('cascade'); // Clave foránea de categorías
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade'); // Clave foránea de usuarios
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones');
    }
};
