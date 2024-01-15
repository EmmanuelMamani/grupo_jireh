@extends("header")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("opciones")
<a href="/menu" class="opciones_head">Inicio</a>
<a href="/registro_cliente" class="opciones_head">Registro</a>
@if (Auth::user()->Rol=='Administrador')
<a href="/reporte_cliente" class="opciones_head">Reporte</a>
@endif
@endsection
@section("contenido")
<form action="{{route("reporte_periodo_ventas",['id'=>$cliente->id])}}" method="GET" id="formulario">
    @csrf
    <h3>Periodo</h3>
    <div class="row"></div>
    <label>Fecha de inicio:</label>
    <div class="row"></div>
    <input type="date" name="inicio" class="form-control">
    @if ($errors->has('inicio'))
    <span class="error text-danger">{{ $errors->first('inicio') }}</span>
    @endif <br>
    <div class="row"></div>
    <label>Fecha de fin:</label>
    <div class="row"></div>
    <input type="date" name="fin" class="form-control">
    @if ($errors->has('fin'))
    <span class="error text-danger">{{ $errors->first('fin') }}</span>
    @endif <br>
    <div class="row" id="cont_btn">
        <div class="col"><a href="/menu" id="cancelar">Cancelar</a></div>
        <div class="col"><button id="enviar">Aceptar</button></div>
    </div>
</form>
@endsection