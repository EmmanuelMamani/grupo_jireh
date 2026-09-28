<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    use HasFactory;

    public function ingreso(){

        return $this->belongsTo(Ingreso::class);
    }

    public function asignador(){

        return $this->belongsTo(User::class, 'asignador_id');
    }
    public function asignado(){

        return $this->belongsTo(User::class, 'asignado_id');
    }
}

