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
        // Relación a través de la tabla ventas (salida_id <-> ingreso_id).
        // Antes sin tabla pivote resolvía a `ingreso_salida` (inexistente).
        return $this->belongsToMany(Ingreso::class, Venta::class, 'salida_id', 'ingreso_id');
    }
}
