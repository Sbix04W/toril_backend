<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    protected $table = 'registro_leche';
    public $timestamps = false;


    protected $fillable = [
        'id',
        'codigo',
        'fecha',
        'hora',
        'litros',
        'precio',
        'id_proveedor',
        'total',
        'obs',
        'iva',


    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id')->with('persona');
    }
}
