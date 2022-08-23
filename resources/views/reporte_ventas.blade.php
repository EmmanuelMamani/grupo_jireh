@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("venta")}}" class="opciones_head">Venta</a>
@if (Auth::user()->Rol=='Administrador')
<a href="{{route("reporte_ventas")}}" class="opciones_head">Reporte</a> 
@endif
<a href="{{route("ventas_pendientes")}}" class="opciones_head">Pendientes</a>
@endsection
@section("estilos")
<link rel="stylesheet" href="{{asset("css/reporte.css")}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
@endsection
@section("contenido")
<h3>Reporte de empleados</h3>
<table id="tabla" class="table ">
    <thead>
      <tr>
        <th>#</th>
        <th>Cliente</th>
        <th>Empleado</th>
        <th>Monto</th>
        <th>Fecha</th>
        <th>Detalle</th>
        <th>Devolver</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($ventas as $key=>$venta)
        <tr class="fila">
          <td>{{$key+1}}</td>
          <td>{{$venta->cliente->Nombre}}</td>
          <td>{{$venta->user->Nombre}}</td>
          <td>{{$venta->salida->Total}}</td>
          <td>{{$venta->created_at->format('Y-m-d')}}</td>
          <td><a href="{{route('venta_detalle',['id'=>$venta->id])}}">Ver detalle</a></td>
          <td><a href="{{route("venta_devolucion",['id'=>$venta->id])}}">Devolver</a></td>
        </tr>
      @endforeach
      
      
    </tbody>
  </table>
  <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
  <script>
         $('#tabla').DataTable({
      responsive:true,
      autoWidth:false,
      "language": {
            "lengthMenu": "Mostrar _MENU_  ",
            "zeroRecords": "No hay resultados",
            "info": "Mostrando la página _PAGE_ de _PAGES_",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search":"Buscar",
            "paginate":{
                  "next":"Siguiente",
                  "previous":"Anterior"
            }
        }
      });
</script>
@endsection