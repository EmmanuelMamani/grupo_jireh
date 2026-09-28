<?php

namespace App\Http\Controllers;

use App\Models\Lista;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index(){
        // Nota: no se usa Estado=0 como badge (36k ventas lo tienen por default;
        // ese flag no distingue pre-ventas reales por confirmar).
        $misPedidos = (int) Lista::where('user_id', Auth::id())->count();
        $deuda = 0;
        if (Auth::user()->Rol == 'Administrador') {
            $deuda = (float) (DB::selectOne("
                SELECT COALESCE(SUM(t.Saldo), 0) AS total FROM (
                    SELECT s.Saldo FROM saldos s
                    INNER JOIN (SELECT cliente_id, MAX(id) AS max_id FROM saldos GROUP BY cliente_id) m
                        ON m.max_id = s.id
                    INNER JOIN clientes c ON c.id = s.cliente_id
                    WHERE c.Activo = 1 AND s.Saldo > 0
                ) AS t
            ")->total ?? 0);
        }
        return view('menu', [
            'mis_pedidos' => $misPedidos,
            'deuda_total' => round($deuda, 2),
        ]);
    }
}
