<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\RuleCantMoldes;
use App\Rules\RuleCliente;
use App\Rules\RuleLote;
use App\Rules\RulePeso;
use App\Rules\RuleProducto;
class devolucionRequest extends FormRequest
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
            "unidades"=>"required|integer",
            "monto"=>"required|numeric"
        ];
    }
    public function messages()
    {
        return[
            "unidades.required"=>"Las unidades son requeridas",
            "unidades.integer"=>"Las unidades deben ser enteros",
            "monto.required"=>"El monto es requerido",
            "monto.numeric"=>"El monto debe ser un número"
        ];
    }
}
