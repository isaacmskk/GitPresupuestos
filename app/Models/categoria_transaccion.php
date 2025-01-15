<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categoria_transaccion extends Model
{
    use HasFactory;

    protected $table = 'categoria_transaccion';
    protected $fillable = [
        'categoria_id',
        'transaccion_id',
    ];
}
