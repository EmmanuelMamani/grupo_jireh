@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("registro_producto")}}" class="opciones_head">Registro</a>
<a href="{{route("reporte_producto")}}" class="opciones_head">Reporte</a>
@endsection
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <form id="form_producto" method="post" action="{{route('registro_producto')}}" class="space-y-4">
      @csrf
      <h3 class="text-xl font-bold text-slate-800">Registro de producto</h3>
      <div>
        <x-ui-label for="nombre">Nombre de producto</x-ui-label>
        <x-ui-input type="text" name="nombre" id="nombre" autocomplete="off" value="{{old('nombre')}}"/>
        <x-ui-error field="nombre"/>
      </div>
      <div>
        <x-ui-label for="tipo">Tipo de producto</x-ui-label>
        <x-ui-select name="tipo" id="tipo">
          <option value="Por Kilo">Por kilo</option>
          <option value="Por Unidad">Por unidad</option>
        </x-ui-select>
      </div>
      <div class="grid grid-cols-2 gap-3 pt-1" id="cont_btn">
        <x-ui-ghost href="{{route('menu')}}" id="cancelar">Cancelar</x-ui-ghost>
        <x-ui-primary id="enviar">Registrar</x-ui-primary>
      </div>
    </form>
  </x-ui-card>
</div>
@endsection
