<?php

namespace App\Http\Requests;

use App\Rules\RuleContraseña;
use Illuminate\Foundation\Http\FormRequest;

class contraseñaRequest extends FormRequest
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
            "actual"=>["required", new RuleContraseña],
            "nueva" =>"required",
        ];
    }
    public function messages()
    {
        return[
            "actual.required"=>"La contraseña actual es obligatoria",
            "nueva.required"=>"La contraseña actual es obligatoria"
        ];}
}
