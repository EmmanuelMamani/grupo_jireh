<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoProveedor extends Model
{
    use HasFactory;
    public function ingreso(){
        return $this->belongsTo(Ingreso::class);
    }
    public function cuenta(){
        return $this->belongsTo(Cuenta::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
