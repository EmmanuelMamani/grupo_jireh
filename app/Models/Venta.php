<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;
    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function ingreso(){
        return $this->belongsTo(Ingreso::class);
    }
    public function salida(){
        return $this->belongsTo(Salida::class);
    }
}
