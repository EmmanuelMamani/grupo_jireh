@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">inicio</a>
<a href="{{route("registro_lote")}}" class="opciones_head">Registro</a>
<a href="{{route("reporte_lotes")}}" class="opciones_head">Reporte</a>
@endsection
@section("estilos")
@include('components.tablas_css')
<link rel="stylesheet" href="{{asset("css/reporte.css")}}">
@endsection
@section("contenido")
<h3>Reporte de lotes</h3>
<table id="tabla" class="table ">
    <thead>
      <tr>
        <th>#</th>
        <th>Proveedor</th>
        <th>Producto</th>
        <th>Fecha</th>
        <th>Unidades</th>
        <th>Unidades vendidas</th>
        <th>Ventas</th>
       
      </tr>
    </thead>
    <tbody>
      @foreach ($lotes as $key=>$lote)
        <tr class="fila">
          <td>{{$key+1}}</td>
          <td>{{$lote->Proveedor}}</td>
          <td>{{$lote->producto->Nombre}} {{$lote->producto->Tipo}}</td>
          <td>{{$lote->created_at->format('d-m-Y')}}</td>
          <td>{{$lote->CantMoldes}}</td>
          <td>{{$lote->salidas->sum('CantMoldes')}}</td>
         
          <td><form action="{{route("reporte_lote_ventas",["id"=>$lote->id])}}" method="get">@csrf <button class="btn btn-warning">Ver Ventas</button></form></td>
          
        </tr>
      @endforeach
    </tbody>
  </table>
  
  @include('components.tablas_js')
  <script>
         $('#tabla').DataTable();
</script>
@endsection