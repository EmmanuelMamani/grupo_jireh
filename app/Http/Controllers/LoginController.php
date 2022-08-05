<?php

namespace App\Http\Controllers;

use App\Http\Requests\loginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
        /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function autentificacion(loginRequest $request){
       
        $usuario = User::query()
            ->where('Usuario', $request['usuario'])
            ->where('Contrasenia', $request['contrasenia'])
            ->where('Activo',1)
            ->first();
        
        if ($usuario) {
                Auth::login($usuario);
                $request->session()->regenerate();
                return redirect()->intended('/menu')->with('id',$usuario->id);
        }  
        return back()->withErrors([
            'contrasenia' => 'Contraseña incorrecta',
        ])->withInput();
    }
}
