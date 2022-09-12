@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("registro_zona")}}" class="opciones_head">Registro</a>
<a href="{{route("reporte_zona")}}" class="opciones_head">Reporte</a>
@endsection
@section("contenido")
<form id="formulario" action="{{route('registro_zona')}}" method="post">
    <h3>Registro de zona</h3>
    @csrf
    <label class="form-label">Nombre de zona:</label>
    <input type="text" name="Nombre" class="form-control" value="{{old('Nombre')}}">
    @if ($errors->has('Nombre'))
      <span class="error text-danger">{{ $errors->first('Nombre') }}</span>
      @endif
      <br>
    <div class="row" id="cont_btn">
        <div class="col"><a href="/menu" id="cancelar">Cancelar</a></div>
        <div class="col"><button id="enviar">Registrar</button></div>
    </div>
</form>
@endsection