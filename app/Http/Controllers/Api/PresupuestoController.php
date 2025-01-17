<?php


namespace App\Http\Controllers\Api;

use App\Models\presupuestos;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PresupuestoController extends Controller
{
    public function index(Request $request)
    {
        $usuario = auth()->user(); // Obtener el usuario autenticado

        // Si se solicita un agrupamiento por mes
        if ($request->has('group_by') && $request->group_by == 'mes') {
            $presupuestos = presupuestos::where('user_id', $usuario->id)
                ->selectRaw('SUM(monto) as monto, MONTH(mes) as mes')
                ->groupBy('mes')
                ->get();
        } else {
            // Si no se solicita agrupamiento por mes, simplemente se obtienen todos los presupuestos
            $presupuestos = presupuestos::where('user_id', $usuario->id)->get();
        }

        $total = $presupuestos->sum('monto'); // Sumar el total de los presupuestos

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
