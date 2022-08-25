<?php

namespace App\Http\Controllers;

use App\Http\Requests\usersRequest;
use App\Models\User;
use App\Notifications\NuevoUsuario;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Notification;

class UserController extends Controller
{
    public function vistaRegistro(){
        return view("registro_empleado");
    }
    
    public function registro(usersRequest $request){
        $usuario=User::where('CI',$request->ci)->get();
        if($usuario->isEmpty()){
            $empleado=new User();
            $empleado->CI=$request->ci;
            $empleado->Nombre=$request->nombre;
            $empleado->Email=$request->email;
            $empleado->Telefono=$request->telefono;
            $empleado->Rol="Empleado";
            $empleado->Usuario=explode(" ",$request->nombre)[0] . $request->ci;
            $caracteres=explode(" ",$request->nombre)[0] . $request->ci; 
            $empleado->Contrasenia=substr(str_shuffle($caracteres), 0, 10);;
            $empleado->save();
            Notification::route('mail', $request->email)->notify(new NuevoUsuario($empleado));
        }else{
            $empleado=User::all()->where('CI',$request->ci)->last();
            $empleado->Activo=1;
            $empleado->save();
            Notification::route('mail', $request->email)->notify(new NuevoUsuario($empleado));
        }
        
        return redirect()->route('registro_empleado')->with('registrar','ok');
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
