@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/venta.css")}}">
@endsection
@section("contenido")
<form id="venta">
    @csrf
    <select name="cliente" id="cliente" class="form-select">
        <option value="0">Cliente 1</option>
    </select>
</form>
@endsection