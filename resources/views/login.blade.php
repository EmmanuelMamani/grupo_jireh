@extends('header')
@section("titulo", "Grupo JIREH")
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("contenido")
<div class="max-w-md mx-auto px-3 py-8">
  <x-ui-card>
    <form action="{{ route('login') }}" method="POST" class="space-y-4">
      @csrf
      <div class="text-center">
        <img src="{{asset('img/logo.png')}}" alt="Grupo Jireh" width="72" class="mx-auto mb-2">
        <h3 class="text-xl font-bold text-slate-800">Inicia sesión</h3>
      </div>
      <div>
        <x-ui-label for="usuario">Usuario</x-ui-label>
        <x-ui-input type="text" name="usuario" id="usuario" value="{{old('usuario')}}" autofocus autocomplete="username"/>
        <x-ui-error field="usuario"/>
      </div>
      <div>
        <x-ui-label for="contrasenia">Contraseña</x-ui-label>
        <x-ui-input type="password" name="contrasenia" id="contrasenia" autocomplete="current-password"/>
        <x-ui-error field="contrasenia"/>
      </div>
      <x-ui-primary>Acceder</x-ui-primary>
    </form>
  </x-ui-card>
</div>
@endsection
