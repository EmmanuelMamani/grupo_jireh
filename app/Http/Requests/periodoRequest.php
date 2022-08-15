<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class periodoRequest extends FormRequest
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
            "inicio"=>"required",
            "fin"=>"required"
        ];
    }
    public function messages()
    {
        return[
            "inicio.required"=> "La fecha de inicio es requerida",
            "fin.required"=> "La fecha de fin es requerida"
        ];
    }
}
