@extends("header")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("opciones")
<a href="{{route('reporte_cliente')}}" class="opciones_head material-symbols-outlined" id="flecha">arrow_back</a>
@endsection
@section("contenido")
<img src="data:image/jpeg;base64,{{ base64_encode($cliente->tienda) }}" width="200">
@endsection