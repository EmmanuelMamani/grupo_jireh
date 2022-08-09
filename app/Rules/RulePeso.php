<?php

namespace App\Rules;

use App\Models\Producto;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\DataAwareRule;

class RulePeso implements Rule,DataAwareRule
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
    public function setData($data)
    {
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
        $bool=true;
        if($this->data['producto']!="Elije un producto"){
            $tipo=Producto::all()->where('id',$this->data['producto'])[0]->Tipo; 
            if($tipo=='Por kilo' && $value=="0.00"){
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
        return 'El campo es obligatorio';
    }
}
