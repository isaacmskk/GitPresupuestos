<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

class BankAccountController extends Controller
{
    // Conectar con la API para listar cuentas bancarias
    public function getBankAccounts()
    {
        $response = Http::withToken(env('GC_ACCESS_TOKEN'))
            ->get(env('GC_API_URL') . '/bank_accounts');

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Error al obtener datos bancarios'], $response->status());
    }

    // Obtener transacciones de una cuenta específica
    public function getTransactions($accountId)
    {
        $response = Http::withToken(env('GC_ACCESS_TOKEN'))
            ->get(env('GC_API_URL') . "/bank_accounts/$accountId/transactions");

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Error al obtener transacciones'], $response->status());
    }
}