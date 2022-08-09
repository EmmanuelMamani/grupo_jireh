<?php

namespace App\Rules;

use App\Models\Asignacion;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\DataAwareRule;


class RuleCantMoldes implements Rule,DataAwareRule
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
        $bool=false;
        $moldes=Asignacion::where('asignado_id',Auth::user()->id)->get();
        foreach ($moldes as $molde) {
            if($molde->ingreso->id==$this->data['lote']){
                if($molde->CantMoldes>=$this->data['cantidad_moldes']){
                    $bool=true;
                }
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
        return 'Cantidad insuficiente de moldes';
    }
}
