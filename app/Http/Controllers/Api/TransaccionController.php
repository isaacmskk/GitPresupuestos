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
            $header = fgetcsv($handle);
    
            while (($data = fgetcsv($handle)) !== false) {
                // Limpiar y extraer los datos del CSV
                $descripcion = trim($data[0] ?? 'Sin descripción'); // Índice 0: Concepto (Descripción)
                $fechaCSV = trim($data[2] ?? now()); // Índice 2: Fecha
                $importeRaw = trim($data[3] ?? '0'); // Índice 3: Importe
    
                // Convertir el importe eliminando "EUR" y reemplazando la coma por un punto
                $importe = floatval(str_replace(',', '.', str_replace('EUR', '', $importeRaw)));
    
                // Validar el formato de la fecha (d/m/Y) y convertirla al formato Y-m-d
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fechaCSV)) {
                    $fecha = \Carbon\Carbon::createFromFormat('d/m/Y', $fechaCSV)->format('Y-m-d');
                } else {
                    // Si la fecha no está en el formato esperado, asignar la fecha actual
                    $fecha = now();
                }
    
                // Crear una nueva transacción usando los datos del CSV
                $transaccion = new transacciones([
                    'descripcion' => $descripcion, // Descripción
                    'monto' => $importe, // Importe
                    'tipo' => ($importe > 0) ? 'ingreso' : 'gasto', // Determinar si es un ingreso o gasto
                    'fecha' => $fecha, // Fecha
                    'categoria_id' => 1, // Asignar una categoría genérica o lógica
                    'user_id' => Auth::id(), // ID del usuario autenticado
                ]);
    
                // Guardar la transacción en la base de datos
                $transaccion->save();
            }
    
            fclose($handle); // Cerrar el archivo después de procesarlo
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

    /**
     * Guardar transacciones importadas desde la API de GoCardless.
     */
    public function saveTransactionsToDatabase($transactions)
    {
        foreach ($transactions as $transaction) {
            transacciones::updateOrCreate(
                ['transaction_id' => $transaction['id']], // Asume que existe esta columna en tu tabla
                [
                    'descripcion' => $transaction['description'],
                    'monto' => $transaction['amount'],
                    'fecha' => $transaction['created_at'],
                    'categoria_id' => null, // Asignar una categoría predeterminada o permitir nulo
                    'user_id' => auth()->user()->id, // Relacionarlo con el usuario actual
                ]
            );
        }
    }
}
