@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("venta")}}" class="opciones_head">Venta</a>
<a href="{{route("reporte_ventas")}}" class="opciones_head">Reporte</a> 
<a href="{{route("ventas_pendientes")}}" class="opciones_head">Pendientes</a>
@endsection
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form action="{{route('editar_venta',["id"=>$venta->id])}}" id="formulario" method="POST">
    <h3>Pre-Venta</h3>
    @csrf
    <label class="form-label">Cliente:</label>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" value="0" id="tipo" name="tipo">
        <label class="form-check-label">
          Cliente empresa
        </label>
    </div>
    @if ($venta->cliente_id != null)
        <select name="cliente" id="cliente" class="form-select">
            <option value={{"$venta->cliente->id"}}>{{$venta->cliente->Nombre}}</option>
        </select>
        @if ($errors->has('cliente'))
            <span class="error text-danger" for="cliente">{{ $errors->first('cliente') }}</span><br>
        @endif  
    @endif
    <label class="form-label">Producto:</label>
    <select name="producto" id="producto" class="form-select">
            <option value="{{$venta->ingreso->producto_id}}">{{$venta->ingreso->producto->Nombre}} {{$venta->ingreso->producto->Tipo}}</option>
    </select>
    @if ($errors->has('producto'))
    <span class="error text-danger" for="producto">{{ $errors->first('producto') }}</span><br>
@endif  
    <label class="form-label">Lote:</label>
    <select name="lote" id="lote" class="form-select">
        <option value="$venta->ingreso_id">{{$venta->ingreso->Proveedor}}</option>
    </select>
    @if ($errors->has('lote'))
    <span class="error text-danger" for="lote">{{ $errors->first('lote') }}</span><br>
@endif 
    <label for="lote" id="cantidad_lotes"></label><br>
    <div class="row">
        <div class="col">
            <label class="form-label">Cantidad de moldes:</label>
        </div>
        
        <div class="col">
            <label class="form-label">Peso total:</label>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <input type="text" name="cantidad_moldes" class="form-control" value="{{$venta->salida->CantMoldes}}" readonly>
            @if ($errors->has('cantidad_moldes'))
            <span class="error text-danger" for="cantidad_moldes">{{ $errors->first('cantidad_moldes') }}</span>
         @endif  
        </div>
        <div class="col">
            <input type="text" name="peso" class="form-control" id="peso" value="{{$venta->salida->Peso}}" readonly>
            @if ($errors->has('peso'))
            <span class="error text-danger" for="peso">{{ $errors->first('peso') }}</span>
         @endif  
        </div>
    </div>
    <label class="form-label">Precio por kilo o unidad:</label>
    <div class="row">
        <div class="col">
            <input type="text" name="precio" id="precio" class="form-control" value="{{$venta->salida->Precio}}">
            @if ($errors->has('precio'))
               <span class="error text-danger" for="precio">{{ $errors->first('precio') }}</span>
            @endif  
        </div>
    </div>
    <label class="form-label">Comprobante:</label>
    @foreach ($comprobantes as $comprobante)
    <img src="data:image/jpeg;base64,<?php
        echo base64_encode($comprobante->Comprobante);
    ?>">
    @endforeach
    <div class="row" id="cont_btn">
        <div class="col"><a id="cancelar" href="/menu">Cancelar</a></div>
        <div class="col"><button id="enviar" type='submit'>Vender</button></div>
    </div>
</form>
@endsection