@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}"  class="opciones_head">Inicio</a>
<a href="/saldos" class="opciones_head">Cobranza</a>
@if (Auth::user()->Rol=='Administrador')
<a href="/saldo_pasado" class="opciones_head">C. Pasados</a>
@endif
@endsection
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario" action="{{route("saldos")}}" method="POST">
    @csrf
    <h3>Cobranza</h3>
    <label class="form-label">Zonas:</label>
    <select name="" id="zona" class="form-select" onchange="cambio()">
        @foreach ($zonas as $zona)
            <option value="{{$zona->id}}">{{$zona->Nombre}}</option>
        @endforeach
    </select>
    <label class="form-label">Cliente:</label>
    <select name="cliente" id="cliente" class="form-select">
        @foreach ($clientes as $cliente )
            @if ($cliente->zona_id == $zonas->first()->id)
                @if ($cliente->saldos->isNotEmpty())
                    @if ($cliente->saldos->last()->Saldo > 0)
                    <option value="{{$cliente->id}}">{{$cliente->Nombre}} Debe:{{$cliente->saldos->last()->Saldo}} Bs</option>
                    @endif
                @endif
            @endif
        @endforeach
    </select>
    <label class="form-label">Monto a pagar:</label>
    <input type="text" name="monto" id="monto" class="form-control"  value="{{old('monto')}}">
    @if ($errors->has('monto'))
    <span class="error text-danger">{{ $errors->first('monto') }}</span>
    @endif <br>
    <div class="row" id="cont_btn">
        <div class="col"><a href="/menu" id="cancelar">Cancelar</a></div>
        <div class="col"><button id="enviar">Pagar</button></div>
    </div>
</form>
<script>
    function cambio(){
        var zona = document.getElementById('zona').value
        var cliente= document.getElementById('cliente')
        cliente.innerHTML=""
        @foreach ($clientes as $cliente )
            var encontrado=false
            if({{$cliente->zona_id}} == zona){
                encontrado=true;
            }
                @if ($cliente->saldos->isNotEmpty())
                    @if ($cliente->saldos->last()->Saldo > 0)
                    if(encontrado==true){
                        cliente.innerHTML+='<option value="{{$cliente->id}}">{{$cliente->Nombre}} Debe:{{$cliente->saldos->last()->Saldo}} Bs</option>'
                    }
                    @endif
                @endif
        @endforeach
    }
</script>
@endsection