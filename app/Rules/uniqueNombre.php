<?php

namespace App\Rules;

use App\Models\Zona;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class uniqueNombre implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {   
        $nombre=str_replace(" ","",$value);
        $resultado=DB::table('zonas')->whereRaw("REPLACE(Nombre,' ','') LIKE '".$nombre."'")->where('Activo',1)->get();
      
        return $resultado->isEmpty();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Ya existe una zona registrada con ese nombre.';
    }
}
