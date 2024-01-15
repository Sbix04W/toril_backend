<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    protected $table = 'rutas';
    public $timestamps = false;


    protected $fillable = [
        'id',
        'origen',
        'destino',
        'descripcion',
        'id_usuario',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id')->with('getPersona');
    }
}
