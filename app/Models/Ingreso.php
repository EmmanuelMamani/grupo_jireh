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
}