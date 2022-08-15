@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/reporte.css")}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
@endsection
@section("contenido")
<h3>Reporte de cuentas</h3>
<table id="tabla" class="table ">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Fecha</th>
        <th>Monto</th>
        <th>Detalle</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($usuarios as $usuario )
        @foreach ($cuentas as $cuenta )
          @if ($usuario->id == $cuenta->user_id)
            <tr class="fila">
              <td>{{$usuario->Nombre}}</td>
              <td>{{$cuenta->Fecha}}</td>
              <td>{{$cuenta->monto}}</td>
              <th><form action="{{route("detalle_cuenta",['id'=>$usuario->id,'fecha'=>$cuenta->Fecha])}}" method="GET">@csrf <button>Detalle</button></form></th>
            </tr>
          @endif
        @endforeach
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