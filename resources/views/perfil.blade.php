@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
@endsection
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <form id="form_clave" method="POST" action="{{route("cambiar_contraseña")}}" class="space-y-4">
      @csrf
      <h3 class="text-xl font-bold text-slate-800">Cambiar contraseña</h3>
      <div>
        <x-ui-label for="actual">Contraseña actual</x-ui-label>
        <x-ui-input type="password" name="actual" id="actual" autocomplete="current-password"/>
        <x-ui-error field="actual"/>
      </div>
      <div>
        <x-ui-label for="nueva">Nueva contraseña</x-ui-label>
        <x-ui-input type="password" name="nueva" id="nueva" autocomplete="new-password"/>
        <x-ui-error field="nueva"/>
      </div>
      <div class="grid grid-cols-2 gap-3 pt-1" id="cont_btn">
        <x-ui-ghost href="{{route('menu')}}" id="cancelar">Cancelar</x-ui-ghost>
        <x-ui-primary id="enviar">Guardar</x-ui-primary>
      </div>
    </form>
  </x-ui-card>
</div>
@endsection
