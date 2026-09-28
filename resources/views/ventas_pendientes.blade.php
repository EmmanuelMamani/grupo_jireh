@extends("header")
@section("titulo", "Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("venta")}}" class="opciones_head">Venta</a>
<a href="{{route("reporte_ventas")}}" class="opciones_head">Reporte</a> 
<a href="{{route("ventas_pendientes")}}" class="opciones_head">Pendientes</a>
@endsection
@section("estilos")
@include('components.tablas_css')
<link rel="stylesheet" href="{{asset("css/reporte.css")}}">
@endsection
@section("contenido")
<h3>Ventas pendientes</h3>
<table id="tabla" class="table">
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Moldes</th>
            <th>Peso</th>
            <th>Total</th>
            <th>Confirmar</th>
            <th>Eliminar</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ventas as $venta )
            <tr class="fila">
                <td>{{$venta->cliente->Nombre}}</td>
                <td>{{$venta->salida->CantMoldes}}</td>
                <td>{{$venta->salida->Peso}}</td>
                <td>{{$venta->salida->Total}}</td>
                <td><form action="{{route("modificar",['id'=>$venta->id,'tipo'=>1])}}" method="post">@csrf<button class="btn btn-success">Confirmar</button></form></td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>

@include('components.tablas_js')
<script>
    $('#tabla').DataTable();
</script>
@endsection