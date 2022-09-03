<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class loteRule extends FormRequest
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
            //
            'proveedor' => 'bail|required|regex:/^[a-zA-Z\s áéíóúÁÉÍÓÚñÑ ()]+$/u',
            'moldes' => 'bail|required|integer',
            'peso' => 'bail|required|numeric',
            'costo'=> 'bail|required|numeric'
        ];
    }
    public function messages()
    {
        return[
            'proveedor.required'=>'El campo proveedor es obligatorio',
            'proveedor.regex' => 'Solo se aceptan caracteres alfabéticos y espacios.',
            'moldes.required'=> 'La cantidad de moldes es obligatoria',
            'moldes.integer' => 'La cantidad de moldes solo pueden ser numeros enteros',
            'peso.required' => 'El peso es requerido',
            'peso.numeric' => 'El peso tiene que ser numeros',
            'costo.required' => 'El costo es obligatorio',
            'costo.numeric' => 'El costo debe ser un número'
        ];
    }
}
