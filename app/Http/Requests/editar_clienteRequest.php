<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class editar_clienteRequest extends FormRequest
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
            'nombre' => 'bail|required|regex:/^[a-zA-Z\s áéíóúÁÉÍÓÚñÑ ()]+$/u',
            'direccion' => 'bail|required'
        ];
    }
    public function messages()
    {
        return[
            'nombre.required'=>'El campo Nombre es obligatorio',
            'nombre.regex' => 'Solo se aceptan caracteres alfabéticos y espacios.',
            'direccion.required' => 'El campo direccion es obligatorio'
        ];
    }
}
