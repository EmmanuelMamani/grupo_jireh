<?php

namespace App\Http\Controllers;

use App\Http\Requests\usersRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function vistaRegistro(){
        return view("registro_empleado");
    }
    
    public function registro(usersRequest $request){
        $empleado=new User();
        $empleado->CI=$request->ci;
        $empleado->Nombre=$request->nombre;
        $empleado->Email=$request->email;
        $empleado->Telefono=$request->telefono;
        $empleado->Rol="Empleado";
        $empleado->Usuario=explode(" ",$request->nombre)[0] . $request->ci;
        $empleado->Contrasenia=$request->ci;
        $empleado->save();
    }

    public function vistaEliminar(){

    }
    
    public function eliminar(){

    }

    public function vistaReporte(){
        $user=User::all();
        return view("reporte_empleados",["empleados"=>$user]);
    }
}
