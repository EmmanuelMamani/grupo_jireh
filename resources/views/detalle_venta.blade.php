@extends("header")
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("opciones")
<a href="{{route('reporte_ventas')}}" class="opciones_head" id="flecha" aria-label="Volver"><x-icon name="arrow_back"/></a>
@endsection
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <h3 class="text-xl font-bold text-slate-800 mb-3">Detalle</h3>
    <dl class="space-y-2 text-sm text-slate-700">
      <div class="flex justify-between gap-3"><dt class="text-slate-500">Fecha</dt><dd class="font-semibold text-right">{{$venta->created_at->format('d-m-Y')}}</dd></div>
      @if ($venta->cliente==null)
      <div class="flex justify-between gap-3"><dt class="text-slate-500">Cliente</dt><dd class="font-semibold text-right">Sin nombre</dd></div>
      @else
      <div class="flex justify-between gap-3"><dt class="text-slate-500">Cliente</dt><dd class="font-semibold text-right">{{$venta->cliente->Nombre}}</dd></div>
      @endif
      <div class="flex justify-between gap-3"><dt class="text-slate-500">Vendedor</dt><dd class="font-semibold text-right">{{$venta->user->Nombre}}</dd></div>
      <div class="flex justify-between gap-3"><dt class="text-slate-500">Producto</dt><dd class="font-semibold text-right">{{$venta->ingreso->producto->Nombre}} {{$venta->ingreso->producto->Tipo}}</dd></div>
      <div class="flex justify-between gap-3"><dt class="text-slate-500">Cantidad</dt><dd class="font-semibold text-right">{{$venta->salida->CantMoldes}}</dd></div>
      <div class="flex justify-between gap-3"><dt class="text-slate-500">Precio/kilo o unidad</dt><dd class="font-semibold text-right">{{$venta->salida->Precio}}</dd></div>
      <div class="flex justify-between gap-3"><dt class="text-slate-500">Peso</dt><dd class="font-semibold text-right">{{$venta->salida->Peso}} Kg.</dd></div>
      <div class="flex justify-between gap-3 border-t border-slate-200 pt-2"><dt class="text-slate-500">Total</dt><dd class="font-bold text-right">Bs {{$venta->salida->Total}}</dd></div>
    </dl>
  </x-ui-card>
</div>
@endsection
