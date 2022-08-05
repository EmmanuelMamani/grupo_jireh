@extends('header')
@section("titulo", "Grupo JIREH")
@section("contenido")
<div id="login">
  <h3>Inicia Sesión</h3>
  <form action={{ route('login') }} method="POST">
    @csrf
    <label class="form-label">Usuario:</label>
    <input type="text" name="usuario" class="form-control" value="{{old('usuario')}}">
    @if ($errors->has('usuario'))
               <span class="error text-danger" for="usuario">{{ $errors->first('usuario') }}</span>
    @endif  
    <br>
    <label class="form-label">Contraseña:</label><br>
    <input type="password" name="contrasenia" class="form-control" value="{{old('contrasenia')}}">
    @if ($errors->has('contrasenia'))
               <span class="error text-danger" for="contrasenia">{{ $errors->first('contrasenia') }}</span>
    @endif  
    <br>
    <br>
    <button id="acceder" type="submit">Acceder</button>
  </form>
</div>
@endsection