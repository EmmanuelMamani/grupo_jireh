@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario">
    <h3>Transferir lote</h3>
    <label class="form-label">Lote:</label>
    <select name="zona" id="lote" class="form-select">
        <option value="0">Elije el lote</option>
        <option value="0">Juan-200-28/07/2022</option>
    </select>
    <label class="form-label">Transferir a:</label>
    <select name="zona" id="transferido" class="form-select">
        <option value="0">Elije a quien transferir</option>
        <option value="0">Administrador</option>
    </select>
    <label class="form-label">Cantidad de moldes:</label>
    <input type="text" name="cantidad_moldes" class="form-control">
    <div class="row" id="cont_btn">
        <div class="col"><button id="cancelar">Cancelar</button></div>
        <div class="col"><button id="enviar">Transferir</button></div>
    </div>
</form>
@endsection
