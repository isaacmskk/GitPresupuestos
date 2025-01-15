<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transacciones extends Model
{
    use HasFactory;

    protected $fillable = [
        'descripcion',
        'monto',
        'tipo',
        'fecha',
        'categoria_id',
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
     * Relación muchos a muchos con las categorías.
     */
    public function categoria()
    {
        return $this->belongsTo(categorias::class);
    }
    
}
