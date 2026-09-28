@extends("header")
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("registro_gasto")}}" class="opciones_head">Registro</a>
<a href="{{route("reporte_diario")}}" class="opciones_head">Reporte</a>
@if (Auth::user()->Rol=='Administrador')
<a href="{{route("reporte_cuenta")}}" class="opciones_head">R. Total</a>
<a href="{{route("cuentas_periodo")}}" class="opciones_head">R. Periodo</a>
<a href="{{route("reporte_historico")}}" class="opciones_head">R Historico</a>
@endif
@endsection
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <form action="{{route("reporte_periodo")}}" method="GET" id="form_periodo" class="space-y-4">
      @csrf
      <h3 class="text-xl font-bold text-slate-800">Periodo</h3>
      <div>
        <x-ui-label for="inicio">Fecha de inicio</x-ui-label>
        <x-ui-input type="date" name="inicio" id="inicio"/>
        <x-ui-error field="inicio"/>
      </div>
      <div>
        <x-ui-label for="fin">Fecha de fin</x-ui-label>
        <x-ui-input type="date" name="fin" id="fin"/>
        <x-ui-error field="fin"/>
      </div>
      <div class="grid grid-cols-2 gap-3 pt-1" id="cont_btn">
        <x-ui-ghost href="{{route('menu')}}" id="cancelar">Cancelar</x-ui-ghost>
        <x-ui-primary id="enviar">Aceptar</x-ui-primary>
      </div>
    </form>
  </x-ui-card>
</div>
@endsection
