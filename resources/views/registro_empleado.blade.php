@extends("header")
@section("titulo" ,"Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("registro_empleado")}}" class="opciones_head">Registro</a>
<a href="{{route("reporte_empleados")}}" class="opciones_head">Reporte</a>
@endsection
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <form id="form_empleado" method="POST" action="{{route('registro_empleado')}}" class="space-y-4">
      @csrf
      <h3 class="text-xl font-bold text-slate-800">Registro de empleado</h3>
      <div>
        <x-ui-label for="nombre">Nombre completo</x-ui-label>
        <x-ui-input type="text" name="nombre" id="nombre" autocomplete="off" value="{{old('nombre')}}"/>
        <x-ui-error field="nombre"/>
      </div>
      <div>
        <x-ui-label for="ci">Cédula de identidad</x-ui-label>
        <x-ui-input type="text" name="ci" id="ci" inputmode="numeric" autocomplete="off" value="{{old('ci')}}"/>
        <x-ui-error field="ci"/>
      </div>
      <div>
        <x-ui-label for="email">Email</x-ui-label>
        <x-ui-input type="email" name="email" id="email" autocomplete="off" value="{{old('email')}}"/>
        <x-ui-error field="email"/>
      </div>
      <div>
        <x-ui-label for="telefono">Teléfono</x-ui-label>
        <x-ui-input type="tel" name="telefono" id="telefono" autocomplete="off" value="{{old('telefono')}}"/>
        <x-ui-error field="telefono"/>
      </div>
      <div>
        <x-ui-label for="usuario">Usuario</x-ui-label>
        <x-ui-input type="text" name="usuario" id="usuario" autocomplete="off" value="{{old('usuario')}}"/>
        <x-ui-error field="usuario"/>
      </div>
      <div>
        <x-ui-label for="contrasenia">Contraseña</x-ui-label>
        <x-ui-input type="password" name="contrasenia" id="contrasenia" autocomplete="new-password"/>
        <x-ui-error field="contrasenia"/>
      </div>
      <div class="grid grid-cols-2 gap-3 pt-1" id="cont_btn">
        <x-ui-ghost href="{{route('menu')}}" id="cancelar">Cancelar</x-ui-ghost>
        <x-ui-primary id="enviar">Registrar</x-ui-primary>
      </div>
    </form>
  </x-ui-card>
</div>
@endsection
