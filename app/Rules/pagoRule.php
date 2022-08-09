<?php

namespace App\Rules;

use App\Models\Cliente;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\DataAwareRule;
class pagoRule implements Rule,DataAwareRule
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
        $alerta=false;
        $cliente=Cliente::all()->where("id",$this->data["cliente"])[0]->saldos->last()->Saldo;
        if($cliente >= $value){
            $alerta=true;
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
        return 'El pago es superior a la deuda';
    }
}
