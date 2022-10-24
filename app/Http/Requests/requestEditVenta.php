<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class requestEditVenta extends FormRequest
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
            'precio'=>'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gt:0'
        ];
    }
    public function messages()
    {
        return[
            'precio.required'=>'El campo es obligatorio',
            'precio.numeric'=>'Solo se admiten números',
            'precio.regex'=>'Máximo 2 decimales',
            'precio.gt'=>'El precio debe ser mayor a 0',
        ];
    }
}
