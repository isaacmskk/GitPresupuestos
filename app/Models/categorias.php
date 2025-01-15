<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categorias extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar de manera masiva.
     */
    protected $fillable = [
        'nombre',
        'user_id',
    ];

    /**
     * Relación con el usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación muchos a muchos con las transacciones.
     */
    public function transacciones()
    {
        return $this->belongsToMany(transacciones::class, 'categoria_transaccion');
    }
}
