<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'personas';
    public $timestamps = false;


    protected $fillable = [
        'id',
        'cifnif',
        'nombre',
        'apellido',
        'correo',
        'telefono',
        'direccion',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id_persona', 'id');
    }

}
