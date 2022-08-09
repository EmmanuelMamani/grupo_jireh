<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class usersRequest extends FormRequest
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
            'nombre' => 'bail|required|regex:/^[a-zA-Z\s áéíóúÁÉÍÓÚñÑ]+$/u|min:3|max:100',
            'ci'=>'bail|required|numeric|digits_between:6,10|unique:users,CI',
            'email'=>'bail|required|email|regex:/^[a-zA-Z\s 0-9 @ . _]+$/|unique:users,Email',
            'telefono'=>'bail|required|numeric|digits_between:6,10|unique:users,Telefono',
        ];
    }
    public function messages()
    {
        return[
            'nombre.regex' => 'Solo se aceptan caracteres alfabéticos y espacios.',
            'nombre.required'=>'El campo nombre es obligatorio',
            'nombre.min'=>'El campo nombre debe ser mayor igual a 3 caracteres',
            'nombre.max'=>'El campo nombre debe ser menor igual a 100 caracteres',

            'ci.unique'=> 'Ya existe un empleado registrado con ese CI.',
            'ci.required'=>'El campo CI es obligatorio', 
            'ci.numeric'=>'El campo CI solo admite números',
            'ci.digits_between'=>'El CI debe tener entre 6 y 10 dígitos',
            'ci.unique'=> 'Ya existe un usuario registrado con ese CI.',
            
            'email.unique'=> 'Ya existe un usuario registrado con ese email',
            'email.required'=> 'El campo email es obligatorio',
            'email.regex'=>'Solo se aceptan caracteres alfanuméricos (sin acento, diéresis y el caracter ñ), caracteres especiales (@ . _)',
            'email.email'=>'El formato del email no es válido',

            'telefono.unique'=> 'Ya existe un usuario registrado con ese telefono',
            'telefono.required'=> 'El campo telefono es obligatorio',
            'telefono.numeric'=>'El campo telefono solo admite números',
            'telefono.digits_between'=>'El telefono debe tener entre 6 y 10 dígitos',

         
        ];
    }
}
