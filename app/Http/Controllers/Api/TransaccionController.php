<?php

namespace App\Http\Controllers\Api;

use App\Models\transacciones;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\categorias;
use Illuminate\Support\Facades\Auth;

class TransaccionController extends Controller
{
    public function index(Request $request)
    {
        $query = transacciones::with(['categoria', 'user']); // Carga las relaciones de categoría y usuario

        if (!$request->has('include_all_users') || $request->include_all_users != 'true') {
            $user = auth()->user();
            $query->where('user_id', $user->id);
        }

        if ($request->has('mes')) {
            $query->whereMonth('fecha', $request->mes);
        }

        if ($request->has('group_by') && $request->group_by == 'mes') {
            $transacciones = $query->selectRaw('SUM(monto) as total, MONTH(fecha) as mes')
                ->groupBy('mes')
                ->get();
        } else {
            if ($request->has('monto')) {
                if ($request->monto == 'mayor') {
                    $transacciones = $query->orderBy('monto', 'desc')->get();
                } elseif ($request->monto == 'menor') {
                    $transacciones = $query->orderBy('monto', 'asc')->get();
                } else {
                    $transacciones = $query->get();
                }
            } else {
                $transacciones = $query->get();
            }
        }

        return response()->json($transacciones);
    }

    public function importCSV(Request $request)
    {
        // Validar que el archivo fue subido y es un CSV
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);
    
        $file = $request->file('csv_file');
    
        // Abrimos el archivo para lectura
        if (($handle = fopen($file, 'r')) !== false) {
            // Leer la primera línea (encabezado) y descartarla
            $header = fgetcsv($handle, 0, ',');
    
            while (($data = fgetcsv($handle, 0, ',')) !== false) {
                // Extraer los datos del CSV
                $descripcion = trim($data[0] ?? 'Sin descripción'); // Índice 0: Concepto (Descripción)
                $fechaCSV = trim($data[2] ?? now()); // Índice 2: Fecha
                
                // Reconstrucción del importe (ya que el CSV separa decimales con coma)
                $importeRaw = trim($data[3] ?? '0') . '.' . trim($data[4] ?? '00'); 
                $importeRaw = str_replace('EUR', '', $importeRaw); // Eliminar "EUR"
    
                // Convertir a número flotante sin perder precisión
                $importe = (float) str_replace(',', '.', $importeRaw);
    
                // Determinar si es un gasto o un ingreso ANTES de convertir a positivo
                $tipo = ($importe < 0) ? 'gasto' : 'ingreso';
    
                // Convertir a positivo el importe para que se almacene correctamente
                $importe = number_format(abs($importe), 2, '.', '');
    
                // Validar y convertir la fecha (si viene en d/m/Y)
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fechaCSV)) {
                    $fecha = \Carbon\Carbon::createFromFormat('d/m/Y', $fechaCSV)->format('Y-m-d');
                } else {
                    $fecha = now();
                }
    
                // Crear una nueva transacción
                $transaccion = new transacciones([
                    'descripcion' => $descripcion,
                    'monto' => $importe,
                    'tipo' => $tipo, // Ahora diferenciamos bien ingreso/gasto
                    'fecha' => $fecha,
                    'categoria_id' => 3, 
                    'user_id' => Auth::id(),
                ]);
    
                $transaccion->save();
            }
    
            fclose($handle);
        }
    
        return response()->json(['message' => 'Transacciones importadas correctamente.']);
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
