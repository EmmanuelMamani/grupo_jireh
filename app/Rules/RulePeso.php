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
            //echo(Producto::all()->where('id',$this->data['producto']));
            $tipo=Producto::all()->where('id',$this->data['producto'])->last()->Tipo; 
           
            if($tipo=='Por Kilo' && $value<=0){
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
        return 'El valor no puede ser menor a 0';
    }
}
