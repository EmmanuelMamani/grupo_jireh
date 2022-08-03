@extends("header")
@section("titulo","ALERTA")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/alerta.css")}}">
@endsection
@section("contenido")
<div id="contenedor">
    <h1>USTED NO TIENE ACCESO AQUÍ</h1><br>
    <img src="{{asset("img/warning.svg")}}" alt="error" width="250" id="warning">
</div>
@endsection