<?php

namespace App\Rules;

use App\Models\Zona;
use Illuminate\Contracts\Validation\Rule;

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
        $bool=true;
        $zonas=Zona::all()->where('Activo',1);
        $nombre=str_replace(" ","",$value);
        $nombre=strtoupper($nombre);
        foreach($zonas as $zona){
            $znombre=str_replace(" ","",$zona->Nombre);
            $znombre=strtoupper($znombre);
            if($znombre===$nombre){
                $bool=false;
            }
        }
        return $bool;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'ya existe una zona con ese nombre';
    }
}
