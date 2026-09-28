<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    use HasFactory;
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function pagos(){
        return $this->hasMany(Pago::class);
    }
    public function pagoProveedors(){
        return $this->hasMany(PagoProveedor::class);
    }
}
