<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class saldoRequest extends FormRequest
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
            'monto'=> 'bail|required|numeric|gt:0',
            'motivo' => 'required'
        ];
    }
    public function messages()
    {
        return[
            'monto.required'=> 'El monto es obligatorio',
            'monto.numeric' => 'El monto debe ser numerico',
            'monto.gt' => 'El monto debe ser mayor a 0',
            'motivo.required' => 'El motivo es obligatorio'
        ];
    }
}
