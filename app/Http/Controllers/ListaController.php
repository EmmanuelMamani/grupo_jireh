<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Lista;
use App\Models\Producto;
use App\Models\Zona;
use Illuminate\Http\Request;

class ListaController extends Controller
{
    //
    public function VistaRegistro(){
        $zonas=Zona::all();
        $clientes=Cliente::all();
        $productos=Producto::all();
        return view("lista_pedidos",["zonas"=>$zonas,"clientes"=>$clientes,"productos"=>$productos]);
    }
    public function Registro(){

    }
    public function reporte(){

    }
}
