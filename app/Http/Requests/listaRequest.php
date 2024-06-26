<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\RuleCliente;
use App\Rules\RuleProducto;
class listaRequest extends FormRequest
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
            'unidades'=>"required|integer"
        ];
    }
    public function messages()
    {
        return[
            "unidades.required"=>"Las unidades son obligatorias",
            "unidades.integer"=>"Las unidades deben ser un numero entero"
        ];
    }
}
