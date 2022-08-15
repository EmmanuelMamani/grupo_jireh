@extends("header")
@section("titulo" ,"Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario" method="POST" action="{{route('registro_empleado')}}">
    @csrf
    <h3>Registro de empleado</h3>
    <label class="form-label">Nombre completo:</label>
    <input type="text" name="nombre" class="form-control" value="{{old('nombre')}}">
    @if ($errors->has('nombre'))
    <span class="error text-danger" for="nombre">{{ $errors->first('nombre') }}</span><br>
    @endif  
    <label class="form-label">Cedula de indentidad:</label>
    <input type="text" name="ci" class="form-control" value="{{old('ci')}}">
    @if ($errors->has('ci'))
    <span class="error text-danger" for="ci">{{ $errors->first('ci') }}</span><br>
    @endif  
    <label class="form-label">Email:</label>
    <input type="text" name="email" class="form-control" value="{{old('email')}}">
    @if ($errors->has('email'))
    <span class="error text-danger" for="email">{{ $errors->first('email') }}</span><br>
    @endif  
    <label class="form-label">Telefono:</label>
    <input type="text" name="telefono" class="form-control" value="{{old('telefono')}}">
    @if ($errors->has('telefono'))
    <span class="error text-danger" for="telefono">{{ $errors->first('telefono') }}</span><br>
    @endif 
    <div class="row" id="cont_btn">
        <div class="col"><a href="menu" id="cancelar">Cancelar</a></div>
        <div class="col"><button id="enviar">Registrar</button></div>
    </div>
</form>
@endsection
