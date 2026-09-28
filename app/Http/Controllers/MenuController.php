<?php

namespace App\Http\Controllers;

use App\Models\Lista;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    public function index(){
        $misPedidos = (int) Lista::where('user_id', Auth::id())->count();
        return view('menu', [
            'mis_pedidos' => $misPedidos,
        ]);
    }
}
