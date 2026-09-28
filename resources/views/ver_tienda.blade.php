@extends("header")
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("opciones")
<a href="{{route('reporte_cliente')}}" class="opciones_head" id="flecha" aria-label="Volver"><x-icon name="arrow_back"/></a>
@endsection
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <h3 class="text-xl font-bold text-slate-800 mb-3">Foto de tienda</h3>
    @if ($cliente->tienda && Storage::disk('public')->exists($cliente->tienda))
    <img src="{{ Storage::disk('public')->url($cliente->tienda) }}" class="rounded-xl w-full" alt="Foto de tienda de {{$cliente->Nombre}}">
    @else
    <p class="text-sm text-slate-500">Sin foto de tienda.</p>
    @endif
  </x-ui-card>
</div>
@endsection
