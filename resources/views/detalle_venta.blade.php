@extends("header")
@section("estilos")
<link rel="stylesheet" href="{{secure_asset("css/formulario.css")}}">
@endsection
@section("opciones")
<a href="{{route('reporte_ventas')}}" class="opciones_head material-symbols-outlined" id="flecha">arrow_back</a>
@endsection
@section("contenido")
<div id="formulario">
    <h3>Detalle</h3>
    <span>Fecha : {{$venta->created_at->format('Y-m-d')}}</span><br>
    @if ($venta->cliente==null)
    <span>Cliente : Sin nombre</span><br>
    @else
    <span>Cliente : {{$venta->cliente->Nombre}}</span><br>
    @endif
    
    <span>Vendedor : {{$venta->user->Nombre}}</span><br>
    <span>Producto : {{$venta->ingreso->producto->Nombre}} {{$venta->ingreso->producto->Tipo}}</span><br>
    <span>Cantidad : {{$venta->salida->CantMoldes}}</span><br>
    <span>Precio/kilo o unidad : {{$venta->salida->Precio}}</span><br>
    <span>Total : {{$venta->salida->Total}}</span><br>
    <span>Comprobantes :</span><br>
    @foreach ($comprobantes as $comprobante)
    <img src="data:image/jpeg;base64,<?php
        echo base64_encode($comprobante->Comprobante);
    ?>">
    @endforeach

</div>
<script>
   // var flecha= document.getElementById("flecha");
   // flecha.style.display="none"
</script>
@endsection