@extends("header")
@section("titulo", "Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario">
    <h3>Registro de gasto</h3>
    <label class="form-label">Monto gastado:</label>
    <input type="text" class="form-control" name="monto">
    <label class="form-label">Detalle del gasto:</label>
    <input type="text" class="form-control" name="detalle">
    <div class="row" id="cont_btn">
        <div class="col"><button id="cancelar">Cancelar</button></div>
        <div class="col"><button id="enviar">Registrar</button></div>
    </div>
</form>
@endsection