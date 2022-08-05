@extends("header")
@section("titulo", "Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario" method="POST" action="{{route('registro_lote')}}">
    @csrf
    <h3>Nuevo lote</h3>
    <label class="form-label">Nombre del proveedor</label>
    <input type="text" name="proveedor" class="form-control">
    @if ($errors->has('proveedor'))
    <span class="error text-danger">{{ $errors->first('proveedor') }}</span>
    @endif <br>
    <label class="form-label">Producto:</label>
    <select name="producto" id="producto" class="form-select">
        @foreach ($productos as $producto )
            <option value="{{$producto->id}}">{{$producto->Nombre}} {{$producto->Tipo}}</option>
        @endforeach
    </select>
    <label class="form-label">Cantidad de moldes</label>
    <input type="text" name="moldes" class="form-control">
    @if ($errors->has('moldes'))
    <span class="error text-danger">{{ $errors->first('moldes') }}</span>
    @endif <br>
    <label class="form-label">Peso total:</label>
    <input type="text" name="peso" class="form-control">
    @if ($errors->has('peso'))
    <span class="error text-danger">{{ $errors->first('peso') }}</span>
    @endif <br>
    <label class="form-label">Costo por kilo o unidad:</label>
    <input type="text" name="costo" class="form-control">
    @if ($errors->has('costo'))
    <span class="error text-danger">{{ $errors->first('costo') }}</span>
    @endif <br>
    <div class="row" id="cont_btn">
        <div class="col"><button id="cancelar">Cancelar</button></div>
        <div class="col"><button id="enviar">Registrar</button></div>
    </div>
</form>
@endsection