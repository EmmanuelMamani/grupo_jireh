@extends("header")
@section("titulo", "Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario">
    <h3>Deudas pasadas</h3>
    <label class="form-label">Cliente:</label>
    <select name="cliente" id="cliente" class="form-select">
        <option value="0">Elige un cliente</option>
    </select>
    <Label class="form-label">Monto:</Label>
    <input type="text" class="form-control">
    <label class="form-label">Motivo de deuda:</label>
    <input type="text" class="form-control">
    <div class="row" id="cont_btn">
        <div class="col"><button id="cancelar">Cancelar</button></div>
        <div class="col"><button id="enviar">Pagar</button></div>
    </div>
</form>
@endsection