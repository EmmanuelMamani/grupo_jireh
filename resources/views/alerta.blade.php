@extends("header")
@section("titulo","ALERTA")
@section("estilos")
<link rel="stylesheet" href="{{secure_asset("css/alerta.css")}}">
@endsection
@section("contenido")
<div id="contenedor">
    <h1>USTED NO TIENE ACCESO AQUÍ</h1><br>
    <img src="{{secure_asset("img/warning.svg")}}" alt="error" width="250" id="warning">
</div>
@endsection