<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    
    protected $table = 'locations';
    public $timestamps = false;


    protected $fillable = [
        'id',
        'fecha',
        'hora',
        'latitude',
        'longitude',
        'id_usuario',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id')->with('getPersona');
    }
}
