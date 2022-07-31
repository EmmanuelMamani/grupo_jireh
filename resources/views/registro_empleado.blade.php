@extends("header")
@section("titulo" ,"Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario">
    <h3>Registro de empleado</h3>
    <label class="form-label">Nombre completo:</label>
    <input type="text" name="nombre" class="form-control">
    <label class="form-label">Cedula de indentidad:</label>
    <input type="text" name="ci" class="form-control">
    <label class="form-label">Email:</label>
    <input type="text" name="email" class="form-control">
    <label class="form-label">Telefono:</label>
    <input type="text" name="telefono" class="form-control">
    <div class="row" id="cont_btn">
        <div class="col"><button id="cancelar">Cancelar</button></div>
        <div class="col"><button id="enviar">Registrar</button></div>
    </div>
</form>
@endsection