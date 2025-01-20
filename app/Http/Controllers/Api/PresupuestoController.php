<?php


namespace App\Http\Controllers\Api;

use App\Models\presupuestos;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PresupuestoController extends Controller
{
  public function index(Request $request)
{
    $usuario = auth()->user();
    $query = presupuestos::where('user_id', $usuario->id);

    // Filtrar por mes si se proporciona el parámetro 'mes'
    if ($request->has('mes') && $request->mes !== 'todos') {
        $query->whereMonth('mes', $request->mes);
    }

    // Agrupamiento por mes si 'group_by' es 'mes'
    if ($request->has('group_by') && $request->group_by == 'mes') {
        $presupuestos = $query->selectRaw('SUM(monto) as monto, MONTH(mes) as mes')
            ->groupBy('mes')
            ->get();
    } else {
        $presupuestos = $query->get();
    }

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
