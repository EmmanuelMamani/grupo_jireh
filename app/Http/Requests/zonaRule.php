<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class zonaRule extends FormRequest
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
            'Nombre' => 'bail|required|unique:zonas|regex:/^[a-zA-Z\s áéíóúÁÉÍÓÚñÑ ()]+$/u'
        ];
    }
    public function messages()
    {
        return[
            'Nombre.required'=>'El campo \'Nombre\' es obligatorio',
            'Nombre.regex' => 'Solo se aceptan caracteres alfabéticos y espacios.',
            'Nombre.unique'=> 'Ya existe una carrera registrada con ese nombre.',
        ];
    }
}
