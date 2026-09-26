<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;
    public function venta(){
        return $this->belongsTo(Venta::class);
    }
    public function saldo(){
        return $this->belongsTo(Saldo::class);
    }
    public function cuenta(){
        return $this->belongsTo(Cuenta::class);
    }
    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }
}
