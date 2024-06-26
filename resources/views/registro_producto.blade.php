@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("registro_producto")}}" class="opciones_head">Registro</a>
<a href="{{route("reporte_producto")}}" class="opciones_head">Reporte</a>
@endsection
@section("contenido")
<form id="formulario" method="post" action="{{route('registro_producto')}}">
    @csrf
    <h3>Registro de producto</h3>
    <label class="form-label">Nombre de producto:</label>
    <input type="text" name="nombre" class="form-control"  value="{{old('nombre')}}">
    @if ($errors->has('nombre'))
    <span class="error text-danger">{{ $errors->first('nombre') }}</span>
    @endif <br>
    <label class="form-label">Tipo de producto:</label>
    <select name="tipo" id="tipo" class="form-select">
        <option value="Por Kilo">Por kilo</option>
        <option value="Por Unidad">Por unidad</option>
    </select>
    <div class="row" id="cont_btn">
        <div class="col"><a href="/menu" id="cancelar">Cancelar</a></div>
        <div class="col"><button id="enviar">Registrar</button></div>
    </div>
</form>
@endsection