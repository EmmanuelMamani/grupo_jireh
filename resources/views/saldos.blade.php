@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="/saldos" class="opciones_head">Saldos</a>
@if (Auth::user()->Rol=='Administrador')
<a href="/saldo_pasado" class="opciones_head">S. Pasados</a>
@endif
@endsection
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario" action="{{route("saldos")}}" method="POST">
    @csrf
    <h3>Pago de saldos</h3>
    <label class="form-label">Cliente:</label>
    <select name="cliente" id="cliente" class="form-select">
        @foreach ($clientes as $cliente )
        @if ($cliente->saldos->isNotEmpty())
            @if ($cliente->saldos->last()->Saldo > 0)
            <option value="{{$cliente->id}}">{{$cliente->Nombre}} Debe:{{$cliente->saldos->last()->Saldo}} Bs</option>
            @endif
        @endif
        @endforeach
    </select>
    <label class="form-label">Monto a pagar:</label>
    <input type="text" name="monto" id="monto" class="form-control">
    @if ($errors->has('monto'))
    <span class="error text-danger">{{ $errors->first('monto') }}</span>
    @endif <br>
    <div class="row" id="cont_btn">
        <div class="col"><a href="/menu" id="cancelar">Cancelar</a></div>
        <div class="col"><button id="enviar">Pagar</button></div>
    </div>
</form>
@endsection