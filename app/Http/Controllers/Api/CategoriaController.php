<?php

namespace App\Http\Controllers\Api;

use App\Models\categorias;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoriaController extends Controller
{
    public function index()
{
    $user_id = auth()->user()->id;

    $categorias = categorias::where('user_id', $user_id)->get();

    return response()->json($categorias);
}


    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);
    
        $user_id = auth()->user()->id;
    
        $categoria = categorias::create([
            'nombre' => $request->nombre,
            'user_id' => $user_id,
        ]);
    
        return response()->json($categoria, 201);
    }
    

}
