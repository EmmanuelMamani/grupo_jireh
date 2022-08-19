<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    use HasFactory;
    public function venta(){
        return $this->hasOne(Venta::class);
    }

    public function lotes(){
        return $this->belongsToMany(Ingreso::class);
    }
}
