@extends("header")
@section("titulo", "Grupo JIREH")
@section("opciones")
<a href="{{route("reporte_lotes")}}" class="opciones_head material-symbols-outlined" id="flecha">arrow_back</a>
@endsection
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario" method="POST" action="{{route("editar_lote",["id"=>$lote->id])}}">
    @csrf
    <h3>Nuevo lote</h3>
    <label class="form-label">Nombre del proveedor</label>
    <input type="text" name="proveedor" class="form-control"  value={{$lote->Proveedor}} readonly>
    @if ($errors->has('proveedor'))
    <span class="error text-danger">{{ $errors->first('proveedor') }}</span>
    @endif <br>
    <label class="form-label">Producto:</label>
    <select name="producto" id="producto" class="form-select">
            <option value="{{$lote->producto_id}}">{{$lote->producto->Nombre}} {{$lote->producto->Tipo}}</option>
    </select>
    <label class="form-label">Cantidad de moldes</label>
    <input type="text" name="moldes" class="form-control"  value={{$lote->CantMoldes}} readonly>
    @if ($errors->has('moldes'))
    <span class="error text-danger">{{ $errors->first('moldes') }}</span>
    @endif <br>
    <label class="form-label">Peso total:</label>
    <input type="text" name="peso" class="form-control"  value={{$lote->Peso}} readonly>
    @if ($errors->has('peso'))
    <span class="error text-danger">{{ $errors->first('peso') }}</span>
    @endif <br>
    <label class="form-label">Costo por kilo o unidad:</label>
    <input type="text" name="costo" class="form-control"  value={{$lote->Precio}}>
    @if ($errors->has('costo'))
    <span class="error text-danger">{{ $errors->first('costo') }}</span>
    @endif <br>
    <div class="row" id="cont_btn">
        <div class="col"><a href="{{route("reporte_lotes")}}" id="cancelar">Cancelar</a></div>
        <div class="col"><button id="enviar">Modificar</button></div>
    </div>
</form>
@endsection