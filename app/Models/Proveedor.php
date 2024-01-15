<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    public $timestamps = false;


    protected $fillable = [
        'id',
        'id_persona',
        'id_ruta',
        'latitud',
        'longitud',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona', 'id');
    }
    public function ruta()
    {
        return $this->belongsTo(Ruta::class, 'id_ruta', 'id');
    }
}
