@extends("header")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("opciones")
<a href="{{route('reporte_cliente')}}" class="opciones_head material-symbols-outlined" id="flecha">arrow_back</a>
@endsection
@section("contenido")
@if ($cliente->tienda && Storage::disk('public')->exists($cliente->tienda))
<img src="{{ Storage::disk('public')->url($cliente->tienda) }}" width="400">
@else
<p>Sin foto de tienda.</p>
@endif
@endsection