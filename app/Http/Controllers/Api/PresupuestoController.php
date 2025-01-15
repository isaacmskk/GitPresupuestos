<?php


namespace App\Http\Controllers\Api;

use App\Models\presupuestos;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PresupuestoController extends Controller
{
    public function index()
{
    $usuario = auth()->user(); // Obtener el usuario autenticado

    // Obtener los presupuestos del usuario autenticado
    $presupuestos = presupuestos::where('user_id', $usuario->id)->with('categoria')->get();

    // Calcular el total de los presupuestos del usuario
    $total = $presupuestos->sum('monto');

    return response()->json([
        'presupuestos' => $presupuestos,
        'total' => $total
    ]);
}


    public function store(Request $request)
{
    $usuario = auth()->user();

    $presupuesto = presupuestos::create([
        'user_id' => $usuario->id,
        'categoria_id' => $request->categoria_id,
        'monto' => $request->monto,
        'mes' => $request->mes,
    ]);

    return response()->json($presupuesto, 201);
}




    public function update(Request $request, $id)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'monto' => 'required|numeric',
            'mes' => 'required|date_format:Y-m',
        ]);

        $presupuesto = presupuestos::findOrFail($id);
        $presupuesto->update($request->all());

        return response()->json($presupuesto);
    }

    public function destroy($id)
    {
        $presupuesto = presupuestos::findOrFail($id);
        $presupuesto->delete();

        return response()->json(null, 204);
    }
}
