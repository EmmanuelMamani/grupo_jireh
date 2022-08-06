<?php

namespace App\Http\Controllers;

use App\Http\Requests\loginRequest;
use App\Models\User;
use Illuminate\Filesystem\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache as FacadesCache;

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
                Auth::login($usuario, $remember = true);
                $request->session()->regenerate();
                return redirect()->intended('/menu');
        }  
        return back()->withErrors([
            'contrasenia' => 'Contraseña incorrecta',
        ])->withInput();
    }

    public function logout(Request $request){
        

       
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
       
        return redirect('/');
    }
}
