<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function vistaRegistro(){
        return view("registro_empleado");
    }
    
    public function registro(){
        $u=User::all();
        echo($u);
    }

    public function vistaEliminar(){

    }
    
    public function eliminar(){

    }

    public function vistaReporte(){
        
    }
}
