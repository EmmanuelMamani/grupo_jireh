<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    use HasFactory;
    public function producto(){
        return $this->belongsTo(Producto::class);
    }
    public function ventas(){
        return $this->hasMany(Venta::class);
    }
    public function asignaciones(){
        return $this->hasMany(Asignacion::class);
    }
    public function salidas(){
        return $this->belongsToMany(Salida::class,Venta::class);
    }
    public function merma(){
        return $this->hasOne(Merma::class);
    }
}
