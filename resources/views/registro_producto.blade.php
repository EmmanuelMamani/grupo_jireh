@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario">
    <h3>Registro de producto</h3>
    <label class="form-label">Nomnre de producto:</label>
    <input type="text" name="nombre" class="form-control">
    <label class="form-label">Tipo de producto:</label>
    <select name="tipo" id="tipo" class="form-select">
        <option value="0">Elije un tipo</option>
        <option value="1">Por kilo</option>
        <option value="2">Por unidad</option>
    </select>
    <div class="row" id="cont_btn">
        <div class="col"><button id="cancelar">Cancelar</button></div>
        <div class="col"><button id="enviar">Registrar</button></div>
    </div>
</form>
@endsection