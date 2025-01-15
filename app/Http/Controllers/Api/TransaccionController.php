<?php

namespace App\Http\Controllers\Api;

use App\Models\transacciones;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\categorias;

class TransaccionController extends Controller
{
    public function index()
    {
        // Obtener el usuario autenticado
        $user = auth()->user();
    
        // Obtener las transacciones del usuario con sus relaciones
        $transacciones = transacciones::with('categoria')
            ->where('user_id', $user->id)
            ->get();
    
        // Retornar la respuesta en formato JSON
        return response()->json($transacciones);
    }
    


    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string',
            'monto' => 'required|numeric',
            'tipo' => 'required|in:ingreso,gasto',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $user_id = auth()->user()->id; // Obtener el ID del usuario autenticado

        $transaccion = transacciones::create([
            'descripcion' => $request->descripcion,
            'monto' => $request->monto,
            'tipo' => $request->tipo,
            'categoria_id' => $request->categoria_id,
            'user_id' => $user_id,
            'fecha' => now(),
        ]);

        return response()->json($transaccion, 201);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'monto' => 'required|numeric',
            'tipo' => 'required|in:ingreso,gasto',
            'descripcion' => 'required|string',
            'fecha' => 'required|date',
        ]);

        $transaccion = transacciones::findOrFail($id);
        $transaccion->update($request->all());

        return response()->json($transaccion);
    }

    public function destroy($id)
    {
        $transaccion = transacciones::findOrFail($id);
        $transaccion->delete();

        return response()->json(null, 204);
    }
}
