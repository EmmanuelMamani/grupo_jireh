@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario">
    <h3>Pago de saldos</h3>
    <label class="form-label">Cliente:</label>
    <select name="cliente" id="cliente" class="form-select">
        <option value="0">Elije un cliente</option>
    </select>
    <h5 id="Saldo">Saldo actual: 0 Bs</h5>
    <label class="form-label">Monto a pagar:</label>
    <input type="text" name="monto" id="monto" class="form-control">
    <div class="row" id="cont_btn">
        <div class="col"><button id="cancelar">Cancelar</button></div>
        <div class="col"><button id="enviar">Pagar</button></div>
    </div>
</form>
@endsection