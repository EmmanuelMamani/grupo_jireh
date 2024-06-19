@extends("header")
@section("titulo", "Grupo JIREH")
@section("opciones")
<a href="/menu" class="opciones_head">Inicio</a>
<a href="/registro_cliente" class="opciones_head">Registro</a>
<a href="/reporte_cliente" class="opciones_head">Reporte</a>
@if (Auth::user()->Rol=='Administrador')

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
        <th>Ubicacion</th>
        <th>Tienda</th>
        <th>Editar</th>
        <th>Eliminar</th>
        <th>Ventas</th>
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
          @if ($cliente->direccion_map==NULL)
            <td>Sin ubicacion</td>
          @else
            <td><a href="{{$cliente->direccion_map}}">Direccion</a></td>
          @endif
          @if ($cliente->tienda==NULL)
            <td>Sin tienda</td>
          @else
            <td>
              <a href="{{route('ver_tienda',["id"=>$cliente->id])}}" class="btn btn-warning">Tienda</a>
            </td>
          @endif
          <td><a href="{{route("editar_cliente",["id"=>$cliente->id])}}" class="btn btn-warning">Editar</a></td>
          <td><form class="Eliminar1" action="{{route("eliminar_cliente",["id"=>$cliente->id])}}" method="post" > @csrf <button class="btn btn-danger">Eliminar</button></form></td>
          <td><a href="{{route("ventas_periodo",["id"=>$cliente->id])}}" class="btn btn-warning">Kardex</a></td>
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
<h3>El total que le deben es {{$total}} Bs.</h3><br>
<h3>Saldos por Zonas</h3>
  @foreach ($zonas as $zona)
      <h4>{{$zona->Nombre}}: {{$zona_saldo[$zona->id]}} Bs</h4>
  @endforeach
@endsection