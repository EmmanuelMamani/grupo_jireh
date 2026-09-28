@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <form method="POST" action="{{route("devolucion",["id"=>$venta->id])}}" id="form_devolucion" class="space-y-4">
      @csrf
      <h3 class="text-xl font-bold text-slate-800">Devolución</h3>
      <p class="text-sm text-slate-600">Producto: {{$venta->ingreso->producto->Nombre}} {{$venta->ingreso->producto->Tipo}}</p>
      <div>
        <x-ui-label for="unidades">Unidades</x-ui-label>
        <x-ui-input type="text" name="unidades" id="unidades" inputmode="numeric" autocomplete="off" value="{{ old('unidades') }}"/>
        <x-ui-error field="unidades"/>
      </div>
      <div>
        <x-ui-label for="monto">Monto</x-ui-label>
        <x-ui-input type="text" name="monto" id="monto" inputmode="decimal" autocomplete="off" value="{{ old('monto') }}"/>
        <x-ui-error field="monto"/>
      </div>
      <div class="grid grid-cols-2 gap-3 pt-1" id="cont_btn">
        <x-ui-ghost href="{{route('reporte_ventas')}}" id="cancelar">Cancelar</x-ui-ghost>
        <x-ui-primary id="enviar">Devolver</x-ui-primary>
      </div>
    </form>
  </x-ui-card>
</div>
@endsection
