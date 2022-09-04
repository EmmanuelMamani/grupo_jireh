<?php

namespace App\Http\Requests;

use App\Rules\RuleCantMoldes;
use App\Rules\RuleCliente;
use App\Rules\RuleLote;
use App\Rules\RulePeso;
use App\Rules\RuleProducto;
use App\Rules\RuleZona;
use Illuminate\Foundation\Http\FormRequest;

class ventaRequest extends FormRequest
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
            'cliente'=>[new RuleCliente],
            'producto'=>[new RuleProducto],
            'lote'=>[new RuleLote],
            'cantidad_moldes'=>['required','numeric','integer',new RuleCantMoldes,"gt:0"],
            'peso'=>['bail',new RulePeso,'numeric','regex:/^[\d]{0,11}(\.[\d]{1,3})?$/'],
            'precio'=>'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/|gt:0',     
            'tipo'=>'boolean',
            'comprobante'=>'required', 
           
        ];
    }

    public function messages()
    {
        return[
            'cantidad_moldes.gt'=>'Los moldes deben ser mayores a 0',
            'cantidad_moldes.required'=> 'El campo es obligatorio',
            'precio.required'=>'El campo es obligatorio',
            'cantidad_moldes.numeric'=>'Solo se admiten números',
            'cantidad_moldes.integer'=>'Solo se admiten números enteros',
            'peso.numeric'=>'Solo se admiten números',
            'peso.regex'=>'Máximo 3 decimales',
            'precio.required'=>'El campo es obligatorio',
            'precio.numeric'=>'Solo se admiten números',
            'precio.regex'=>'Máximo 2 decimales',
            'precio.gt'=>'El precio debe ser mayor a 0',
            'comprobante.required'=>'El campo es obligatorio'
        ];
    }
}

