<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recibo extends Model
{
    protected $table = 'recibos';
    public $timestamps = false;


    protected $fillable = [
        'id',
        'codigo',
        'estado',
        'fecha',
        'fechapag',
        'importe',
        'id_registro'
    ];

    public function registro()
    {
        return $this->belongsTo(Registro::class, 'id_registro', 'id')->with('proveedor');
    }
}
