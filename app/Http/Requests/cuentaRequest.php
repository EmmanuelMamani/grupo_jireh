<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class cuentaRequest extends FormRequest
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
            'monto'=>'bail|required|numeric',
            'detalle' => 'required'
        ];
    }
    public function messages()
    {
        return[
            'monto.required'=> 'El monto es obligatorio',
            'monto.numeric' => 'El monto debe ser un numero',
            'detalle' => 'El detalle es obligatorio'
        ];}
}
