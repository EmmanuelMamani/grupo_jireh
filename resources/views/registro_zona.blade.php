@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("registro_zona")}}" class="opciones_head">Registro</a>
<a href="{{route("reporte_zona")}}" class="opciones_head">Reporte</a>
@endsection
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <form id="form_zona" action="{{route('registro_zona')}}" method="post" class="space-y-4">
      <h3 class="text-xl font-bold text-slate-800">Registro de zona</h3>
      @csrf
      <div>
        <x-ui-label for="Nombre">Nombre de zona</x-ui-label>
        <x-ui-input type="text" name="Nombre" id="Nombre" autocomplete="off" value="{{old('Nombre')}}"/>
        <x-ui-error field="Nombre"/>
      </div>
      <div class="grid grid-cols-2 gap-3 pt-1" id="cont_btn">
        <x-ui-ghost href="{{route('menu')}}" id="cancelar">Cancelar</x-ui-ghost>
        <x-ui-primary id="enviar">Registrar</x-ui-primary>
      </div>
    </form>
  </x-ui-card>
</div>
@endsection
