<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merma extends Model
{
    use HasFactory;
    public function ingreso(){
        return $this->belongsTo(Ingreso::class);
    }
}
