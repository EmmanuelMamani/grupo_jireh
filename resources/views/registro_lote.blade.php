@extends("header")
@section("titulo", "Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario">
    <h3>Nuevo lote</h3>
    <label class="form-label">Nombre del proveedor</label>
    <input type="text" name="proveedor" class="form-control">
    <label class="form-label">Producto:</label>
    <select name="producto" id="producto" class="form-select">
        <option value="0">Elije un producto</option>
        <option value="0">Mozarella por kilo</option>
    </select>
    <label class="form-label">Cantidad de moldes</label>
    <input type="text" name="moldes" class="form-control">
    <label class="form-label">Peso total:</label>
    <input type="text" name="peso" class="form-control">
    <label class="form-label">Costo por kilo o unidad:</label>
    <input type="text" name="costo_kilo" class="form-control">
    <div class="row" id="cont_btn">
        <div class="col"><button id="cancelar">Cancelar</button></div>
        <div class="col"><button id="enviar">Registrar</button></div>
    </div>
</form>
@endsection