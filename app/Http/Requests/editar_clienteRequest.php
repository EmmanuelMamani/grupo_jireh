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
            'direccion' => 'bail|required',
            'tienda' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg,webp|max:4096'
        ];
    }
    public function messages()
    {
        return[
            'nombre.required'=>'El campo Nombre es obligatorio',
            'nombre.regex' => 'Solo se aceptan caracteres alfabéticos y espacios.',
            'direccion.required' => 'El campo direccion es obligatorio',
            'tienda.image' => 'El archivo debe ser una imagen.',
            'tienda.mimes' => 'Formato no permitido. Use jpeg, jpg, png, gif, svg o webp.',
            'tienda.max' => 'La imagen no debe superar los 4 MB.'
        ];
    }
}
