<?php

namespace App\Http\Requests;

use App\Rules\RulePeso;
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
            'moldes' => 'bail|required|integer|gt:0',
            'peso' => ['bail','required','numeric',new RulePeso],
            'costo'=> 'bail|required|numeric|gt:0'
        ];
    }
    public function messages()
    {
        return[
            'proveedor.required'=>'El campo proveedor es obligatorio',
            'proveedor.regex' => 'Solo se aceptan caracteres alfabéticos y espacios.',
            'moldes.required'=> 'La cantidad de moldes es obligatoria',
            'moldes.integer' => 'La cantidad de moldes solo pueden ser numeros enteros',
            'moldes.gt'=>'Los moldes deben ser mayor a 0',
            'peso.required' => 'El peso es requerido',
            'peso.numeric' => 'El peso tiene que ser numeros',
            'peso.gte'=>'El peso debe ser mayor o igual a 0',
            'costo.required' => 'El costo es obligatorio',
            'costo.numeric' => 'El costo debe ser un número',
            'costo.gt' => 'El costo debe ser mayor a 0'
        ];
    }
}
