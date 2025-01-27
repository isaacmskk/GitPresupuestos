<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Transacciones;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RandomUserController extends Controller
{
    public function generateUserWithTransactions(Request $request)
    {
        // Obtener datos de la API de RandomUser
        $response = Http::get('https://randomuser.me/api/');
        $data = $response->json();
    
        if (!isset($data['results'][0])) {
            return response()->json(['error' => 'Error al obtener datos de RandomUser'], 500);
        }
    
        $randomUser = $data['results'][0];
    
        // Crear usuario
        $user = User::create([
            'name' => $randomUser['name']['first'] . ' ' . $randomUser['name']['last'],
            'email' => $randomUser['email'],
            'password' => bcrypt('password'),
            'surname1' => $randomUser['name']['first'], // Modifica según tus datos
            'surname2' => $randomUser['name']['last'],  // Modifica según tus datos
            'random_generated' => true,
        ]);
    
        // Crear una transacción aleatoria asociada al usuario
        $monto = rand(100, 1000);
        $transaccion = Transacciones::create([
            'descripcion' => 'Transacción generada automáticamente',
            'monto' => $monto,
            'tipo' => rand(0, 1) ? 'ingreso' : 'gasto',
            'fecha' => now(),
            'categoria_id' => 1, // ID de una categoría predeterminada
            'user_id' => $user->id,
        ]);
    
        // Cargar las relaciones del usuario
        $user->load('transacciones');
    
        return response()->json([
            'message' => 'Usuario y transacción creados correctamente',
            'user' => $user,
            'transacciones' => $user->transacciones,
        ], 201);
    }
    
}
