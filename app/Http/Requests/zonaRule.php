<?php

namespace App\Http\Requests;

use App\Rules\uniqueNombre;
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
            'Nombre' => ['bail','required','regex:/^[a-zA-Z\s áéíóúÁÉÍÓÚñÑ ()]+$/u',new uniqueNombre()]
        ];
    }
    public function messages()
    {
        return[
            'Nombre.required'=>'El campo es obligatorio',
            'Nombre.regex' => 'Solo se aceptan caracteres alfabéticos y espacios.',
        ];
    }
}
