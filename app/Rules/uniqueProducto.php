<?php

namespace App\Rules;

use App\Models\Producto;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class uniqueProducto implements Rule,DataAwareRule
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
    protected $data;
    public function setData($data)    {
        $this->data=$data;
        return $this;
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
        $resultado=DB::table('productos')->whereRaw("REPLACE(Nombre,' ','') LIKE '".$nombre."'")->where('Tipo',$this->data['tipo'])->where('Activo',1)->get();
      
        return $resultado->isEmpty();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Ya existe un producto con ese nombre y tipo';
    }
}
