<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    public function zona(){
        return $this->belongsTo(Zona::class);
    }
    public function ventas(){
        return $this->hasMany(Venta::class);
    }
}
