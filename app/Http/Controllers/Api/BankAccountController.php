<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Api\TransaccionController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

class BankAccountController extends Controller
{
    // Conectar con la API para listar cuentas bancarias
    public function getBankAccounts()
    {
        try {
            $response = Http::withToken(env('GC_ACCESS_TOKEN'))
                ->get(env('GC_API_URL') . '/bank_accounts');

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(['error' => 'Error al obtener datos bancarios'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error de conexión con la API: ' . $e->getMessage()], 500);
        }
    }


    // Obtener transacciones de una cuenta específica
    public function getTransactions($accountId)
    {
        try {
            $response = Http::withToken(env('GC_ACCESS_TOKEN'))
                ->get(env('GC_API_URL') . "/bank_accounts/$accountId/transactions");

            if ($response->successful()) {
                $data = $response->json();

                // Guardar las transacciones en la base de datos
                $transaccionController = new TransaccionController();
                $transaccionController->saveTransactionsToDatabase($data['transactions']);

                return response()->json($data);
            }

            return response()->json(['error' => 'Error al obtener transacciones'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error de conexión con la API: ' . $e->getMessage()], 500);
        }
    }
}
