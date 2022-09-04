<?php

namespace App\Http\Requests;

use App\Rules\pagoRule;
use Illuminate\Foundation\Http\FormRequest;

class pagoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "monto"=>["bail","required","numeric",new pagoRule,"gt:0"]
        ];
    }
    public function messages()
    {
        return[
            "monto.required"=> "El monto es requerido",
            "monto.numeric"=> "El monto debe ser un numero",
            "monto.gt"=>"El monto debe ser mayor a 0"
        ];
    }
}
