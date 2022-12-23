@extends("header")
@section("titulo", "Grupo JIREH")
@section("opciones")
<a href="/menu" class="opciones_head">Inicio</a>
<a href="/registro_cliente" class="opciones_head">Registro</a>
@if (Auth::user()->Rol=='Administrador')
<a href="/reporte_cliente" class="opciones_head">Reporte</a>
@endif
@endsection
@section("estilos")
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="{{asset("css/reporte.css")}}">
@endsection
@section("contenido")
<h3>Reporte de clientes</h3>
<table id="tabla" class="table ">
    <thead>
      <tr>
        <th>#</th>
        <th>Nombre</th>
        <th>Teléfono</th>
        <th>Deuda</th>
        <th>Eliminar</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($clientes as  $key=>$cliente)
        <tr class="fila">
          <td>{{$key+1}}</td>
          <td>{{$cliente->Nombre}}</td>
          <td>{{$cliente->Telefono}}</td>
          @if ($cliente->saldos->isNotEmpty())
          <td>{{$cliente->saldos->last()->Saldo}}</td>
          @else
            <td>0</td>
          @endif
          <td><form class="Eliminar1" action="{{route("eliminar_cliente",["id"=>$cliente->id])}}" method="post" > @csrf <button class="btn btn-danger">Eliminar</button></form></td>
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
      $('.Eliminar1').submit(function(e){
            e.preventDefault();
            Swal.fire({
            title: '¿Estás seguro que quieres eliminar el cliente?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí',
            cancelButtonText: 'No'
            }).then((result) => {
                  if (result.isConfirmed) {
                  this.submit();
            }
            })
      });
</script>
<h3>El total que le deben es {{$total}} Bs.</h3>
@endsection