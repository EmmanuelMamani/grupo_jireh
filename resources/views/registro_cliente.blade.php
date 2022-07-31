@extends("header")
@section("titulo", "Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario">
    <h3>Registro de cliente</h3>
    <label class="form-label">Nombre completo:</label>
    <input type="text" name="nombre"  class="form-control">
    <label class="form-label">Telefono:</label>
    <input type="text" name="telefono"  class="form-control">
    <label class="form-label">Direccion:</label>
    <input type="text" name="direccion"  class="form-control">
    <label class="form-label">Zona:</label>
    <select name="zona" id="zona" class="form-select">
        <option value="0">Elegir zona:</option>
        <option value="0">Zona norte</option>
    </select>
    <div class="row" id="cont_btn">
        <div class="col"><button id="cancelar">Cancelar</button></div>
        <div class="col"><button id="enviar">Registrar</button></div>
    </div>
</form>
@endsection