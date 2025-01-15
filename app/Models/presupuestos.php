<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class presupuestos extends Model
{
    protected $fillable = [
        'user_id',
        'categoria_id',
        'monto',
        'mes',
    ];

    public function store(Request $request)
    {
        $usuario = auth()->user();

        // Verifica si el usuario está autenticado
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        // Validar el resto de los datos
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'monto' => 'required|numeric',
            'mes' => 'required|date_format:Y-m',
        ]);

        // Crear el presupuesto con el user_id del usuario autenticado
        $presupuesto = presupuestos::create([
            'user_id' => $usuario->id,
            'categoria_id' => $request->categoria_id,
            'monto' => $request->monto,
            'mes' => $request->mes,
        ]);

        return response()->json($presupuesto, 201);
    }

    public function categoria()
    {
        return $this->belongsTo(categorias::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    use HasFactory;
}
