<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saldos extends Model
{
    
    protected $table = 'saldos';
    public $timestamps = false;


    protected $fillable = [
        'id',
        'id_usuario',
        'monto_total',
        'monto_restante',
        'fecha',

    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id')->with('getPersona');
    }
}
