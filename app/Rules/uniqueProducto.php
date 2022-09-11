<?php

namespace App\Rules;

use App\Models\Producto;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

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
        $alerta=true;
        $productos=Producto::all()->where("Activo",1);
        $nombre=$this->data['nombre'];
        $tipo=$this->data['tipo'];
        foreach ($productos as $producto){
            if($producto->Nombre == $nombre && $producto->Tipo==$tipo){
                $alerta=false;
            }
        }
        return $alerta;
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
