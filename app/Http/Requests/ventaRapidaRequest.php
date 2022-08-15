<?php

namespace App\Http\Requests;

use App\Rules\RuleCantMoldes;
use App\Rules\RuleLote;
use App\Rules\RulePeso;
use App\Rules\RuleProducto;
use Illuminate\Foundation\Http\FormRequest;

class ventaRapidaRequest extends FormRequest
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
            'producto'=>[new RuleProducto],
            'lote'=>[new RuleLote],
            'centavos'=>'boolean',
            'cantidad_moldes'=>['required','numeric','integer',new RuleCantMoldes],
            'peso'=>['bail',new RulePeso,'numeric','regex:/^[\d]{0,11}(\.[\d]{1,3})?$/'],
            'precio'=>'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/', 
            'comprobante'=>'required', 
        ];
    }

    public function messages()
    {
        return[
            
            'cantidad_moldes.required'=> 'El campo es obligatorio',
            'precio.required'=>'El campo es obligatorio',
            'cantidad_moldes.numeric'=>'Solo se admiten números',
            'cantidad_moldes.integer'=>'Solo se admiten números enteros',
            'peso.numeric'=>'Solo se admiten números',
            'peso.regex'=>'Máximo 3 decimales',
            'precio.required'=>'El campo es obligatorio',
            'precio.numeric'=>'Solo se admiten números',
            'precio.regex'=>'Máximo 2 decimales',
            'comprobante.required'=>'El campo es obligatorio'
        ];
    }
}
