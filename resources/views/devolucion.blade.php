@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form action="" id="formulario">
    <h3>Devolución</h3>
    <label class="form-label">Producto: {{$venta->ingreso->producto->Nombre}} {{$venta->ingreso->producto->Tipo}}</label><br>
    <label class="form-label">Unidades:</label>
    <input type="text" class="form-control" name="unidades">
    @if ($errors->has('unidades'))
    <span class="error text-danger" for="precio">{{ $errors->first('unidades') }}</span>
    @endif  
    <label class="form-label">Monto:</label>
    <input type="text" class="form-control" name="monto">
    @if ($errors->has('monto'))
    <span class="error text-danger" for="precio">{{ $errors->first('monto') }}</span>
 @endif  
    <div class="row" id="cont_btn">
        <div class="col"><a href="/menu" id="cancelar">Cancelar</a></div>
        <div class="col"><button id="enviar">Devolver</button></div>
    </div>
</form>
@endsection

