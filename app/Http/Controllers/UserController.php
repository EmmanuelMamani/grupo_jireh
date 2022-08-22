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
    
    public function eliminar($id){
        $empleado=User::find($id);
       $empleado->Activo=0;
       $empleado->save();
       return redirect()->route('reporte_empleados')->with('eliminar', 'ok');
    }

    public function vistaReporte(){
        $user=User::all()->where("Activo",1);
        return view("reporte_empleados",["empleados"=>$user]);
    }
}
