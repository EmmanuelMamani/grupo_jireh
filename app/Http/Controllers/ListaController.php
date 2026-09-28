<?php

namespace App\Http\Controllers;

use App\Http\Requests\listaRequest;
use App\Models\Asignacion;
use App\Models\Cliente;
use App\Models\Ingreso;
use App\Models\Lista;
use App\Models\Producto;
use App\Models\Zona;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListaController extends Controller
{
    //
    public function VistaRegistro(){
        $zonas=Zona::all();
        $clientes=Cliente::all()->where('Activo',1);
        $productos=Producto::all();
        return view("lista_pedidos",["zonas"=>$zonas,"clientes"=>$clientes,"productos"=>$productos]);
    }
    public function Registro(listaRequest $request){
        $lista=new Lista();
        $lista->cliente_id=$request->cliente;
        $lista->producto_id=$request->producto;
        $lista->user_id=Auth::user()->id;
        $lista->Unidades=$request->unidades;
        $lista->save();
        return redirect()->route('registro_lista')->with('registrar','ok');
    }
    public function reporte(){
        $listas=Lista::with('cliente', 'producto')->where("user_id",Auth::user()->id)->latest()->get();
        $usuarios=User::where("id","!=",Auth::user()->id)->select('id','Nombre')->get();
        return view("lista_reporte",["listas"=>$listas,"usuarios"=>$usuarios]);
    }

    public function eliminar(Request $request,$id){
        $lista=Lista::find($id);
        $lista->delete();
        return redirect()->route('lista_reporte')->with('eliminar','ok');
    }

    public function completar($id){
        $lista=Lista::find($id);
        $lotes=Asignacion::where('asignado_id',Auth::user()->id)->where('CantMoldes','>',0)->get();
        return view("completar_venta",["lista"=>$lista,"lotes"=>$lotes]);
    }
    public function transferir(Request $request){
        $request->validate([
            'lista' => 'required|string',
            'user' => 'required|exists:users,id',
        ]);
        $ids=array_filter(array_map('intval', explode(',', $request->lista)));
        if (empty($ids)) {
            return redirect()->route('lista_reporte')->withErrors(['lista' => 'Selecciona al menos un pedido.']);
        }
        DB::beginTransaction();
        try {
            Lista::whereIn('id', $ids)->where('user_id', Auth::user()->id)->update(['user_id' => $request->user]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return redirect()->route('lista_reporte')->with('registrar', 'ok');
    }
}
