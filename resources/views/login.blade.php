@extends('header')
@section("titulo", "Grupo JIREH")
@section("contenido")
<div id="login">
  <h3>Inicia Sesión</h3>
  <form action="{{ route('login') }}" method="POST">
    @csrf
    <label class="form-label" for="usuario">Usuario:</label>
    <input type="text" name="usuario" id="usuario" class="form-control" value="{{old('usuario')}}" autofocus autocomplete="username">
    @if ($errors->has('usuario'))
               <span class="error text-danger" for="usuario">{{ $errors->first('usuario') }}</span>
    @endif  
    <br>
    <label class="form-label" for="contrasenia">Contraseña:</label><br>
    <input type="password" name="contrasenia" id="contrasenia" class="form-control" autocomplete="current-password">
    @if ($errors->has('contrasenia'))
               <span class="error text-danger" for="contrasenia">{{ $errors->first('contrasenia') }}</span>
    @endif  
    <br>
    <br>
    <button id="acceder" type="submit">Acceder</button>
  </form>
</div>
@endsection