@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
@endsection
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form  id="formulario" method="POST" action="{{route("cambiar_contraseña")}}">
    @csrf
    <h3>Cambiar contraseña</h3>
    <label class="form-label">Contraseña actual</label>
    <input type="password" name="actual" class="form-control">
    @if ($errors->has('actual'))
    <span class="error text-danger">{{ $errors->first('actual') }}</span>
    @endif <br>
    <label class="form-label">Nueva contraseña:</label>
    <input type="password" name="nueva" class="form-control">
    @if ($errors->has('nueva'))
    <span class="error text-danger">{{ $errors->first('nueva') }}</span>
    @endif <br>
    <div class="row" id="cont_btn">
        <div class="col"><a href="/menu" id="cancelar">Cancelar</a></div>
        <div class="col"><button id="enviar">Registrar</button></div>
    </div>
</form>
@endsection
