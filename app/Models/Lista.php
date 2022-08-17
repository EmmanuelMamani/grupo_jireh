<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lista extends Model
{
    use HasFactory;
    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }
    public function producto(){
        return $this->belongsTo(Producto::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
