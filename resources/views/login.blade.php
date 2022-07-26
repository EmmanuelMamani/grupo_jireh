@extends('header')
@section("titulo", "Grupo JIREH")
@section("contenido")
<div id="login">
  <h3>Inicia Sesión</h3>
  <form>
    @csrf
    <label class="form-label">Usuario:</label><br>
    <input type="text" name="usuario" class="form-control">
    <label class="form-label">Contraseña:</label><br>
    <input type="password" name="contraseña" class="form-control"><br>
    <a id="acceder" href="#">Acceder</a>
  </form>
</div>
@endsection