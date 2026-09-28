<?php

namespace App\Http\Controllers;

use App\Http\Requests\clienteRequest;
use App\Models\Cliente;
use App\Models\Zona;
use Illuminate\Http\Request;
use App\Http\Requests\editar_clienteRequest;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
class ClienteController extends Controller
{
    public function vistaRegistro(){
        $zonas=Zona::all();
        return view("registro_cliente",["zonas" => $zonas]);
    }
    
    public function registro(clienteRequest $request){
        $existe=Cliente::where('Telefono',$request->telefono)->get();
        if($existe->isEmpty()){
            $cliente= new Cliente();
            $cliente->Nombre= $request->nombre;
            $cliente->Direccion= $request->direccion;
            $cliente->zona_id=$request->zona;
            $cliente->Telefono= $request->telefono;
            $cliente->save();
        }else{
            $cliente=Cliente::firstwhere('Telefono',$request->telefono);
            $cliente->Nombre=$request->nombre;
            $cliente->Activo=true;
            $cliente->Direccion=$request->direccion;
            $cliente->zona_id=$request->zona;
            $cliente->save();
        }
       
        return redirect()->route('registro_cliente')->with('registrar', 'ok');

    }

    public function vistaEliminar(){

    }
    
    public function eliminar($id){
        $cliente=Cliente::find($id);
        $cliente->Activo=0;
        $cliente->save();
        return redirect()->route('reporte_cliente')->with('eliminar', 'ok');
    }

    public function vistaReporte(){
        $clientes = Cliente::select([
            'id',
            'Nombre',
            'zona_id',
            'Telefono',
            'direccion_map',
            'Activo',
            \DB::raw('CASE WHEN tienda IS NOT NULL THEN "si" ELSE "no" END as getTienda')
        ])
        ->with('ultimoSaldo', 'zona:id,Nombre')
        ->where('Activo', 1)
        ->get();
        $zonas=Zona::all();
        $zona_saldo = [];
        foreach($zonas as $zona){
            $zona_saldo[$zona->id]=0;
        }
        $total=0;
        $conDeuda=0;
        foreach ($clientes as $cliente){
            $saldo = $cliente->ultimoSaldo ? (float) $cliente->ultimoSaldo->Saldo : 0;
            $cliente->setAttribute('deuda_actual', $saldo);
            if($saldo > 0){
                $total += $saldo;
                $conDeuda++;
                $zona_saldo[$cliente->zona_id] = ($zona_saldo[$cliente->zona_id] ?? 0) + $saldo;
            }
        }
        $topZonaId = null;
        $topZonaMonto = 0;
        foreach($zona_saldo as $zid => $monto){
            if($monto > $topZonaMonto){
                $topZonaMonto = $monto;
                $topZonaId = $zid;
            }
        }
        $kpis = [
            'total' => round($total, 2),
            'con_deuda' => $conDeuda,
            'sin_deuda' => $clientes->count() - $conDeuda,
            'ticket' => $conDeuda > 0 ? round($total / $conDeuda, 2) : 0,
            'top_zona' => $topZonaId ? ($zonas->firstWhere('id', $topZonaId)->Nombre ?? '—') : '—',
            'top_zona_monto' => round($topZonaMonto, 2),
        ];
        return view("reporte_cliente",['clientes'=>$clientes, 'total' =>$total,'zonas'=>$zonas,'zona_saldo'=>$zona_saldo,'kpis'=>$kpis]);
    }

    public function vistaEditar($id){
        $cliente= Cliente::find($id);
        $zonas=Zona::all();
        return view('editar_cliente',['cliente'=>$cliente,'zonas'=>$zonas]);
    }
    public function editar(editar_clienteRequest $request, $id)
    {
        $cliente = Cliente::find($id);
        $cliente->Nombre = $request->nombre;
        $cliente->Direccion = $request->direccion;
        $cliente->zona_id = $request->zona;
        $cliente->Telefono = $request->telefono;
        if ($request->mapa != null) {
            $cliente->direccion_map = $request->mapa;
        }
    
        if ($request->hasFile('tienda')) {
            $imagen = $request->file('tienda');

            $anterior = $cliente->getRawOriginal('tienda');
            if (is_string($anterior) && $anterior !== '' && Storage::disk('public')->exists($anterior)) {
                Storage::disk('public')->delete($anterior);
            }

            if (strtolower($imagen->getClientOriginalExtension()) === 'svg') {
                $path = $imagen->storeAs(
                    'tiendas',
                    'cliente_' . $cliente->id . '_' . time() . '.svg',
                    'public'
                );
            } else {
                $img = Image::make($imagen->getRealPath())
                    ->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('jpg', 80);
                $path = 'tiendas/cliente_' . $cliente->id . '_' . time() . '.jpg';
                Storage::disk('public')->put($path, (string) $img);
            }

            $cliente->tienda = $path;
        }
    
        $cliente->save();
        return redirect()->route('reporte_cliente')->with('editar', 'ok');
    }
    
    public function ver_tienda($id){
        $cliente = Cliente::find($id);
        return view('ver_tienda',['cliente'=>$cliente]);
    }
}