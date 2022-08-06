@extends("header")
@section("titulo", "Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario" method="POST" action="{{route("registro_gasto")}}">
    @csrf
    <h3>Registro de gasto</h3>
    <label class="form-label">Monto gastado:</label>
    <input type="text" class="form-control" name="monto">
    @if ($errors->has('monto'))
    <span class="error text-danger">{{ $errors->first('monto') }}</span>
    @endif <br>
    <label class="form-label">Detalle del gasto:</label>
    <input type="text" class="form-control" name="detalle">
    @if ($errors->has('detalle'))
    <span class="error text-danger">{{ $errors->first('detalle') }}</span>
    @endif <br>
    <div class="row" id="cont_btn">
        <div class="col"><button id="cancelar">Cancelar</button></div>
        <div class="col"><button id="enviar">Registrar</button></div>
    </div>
</form>
@endsection