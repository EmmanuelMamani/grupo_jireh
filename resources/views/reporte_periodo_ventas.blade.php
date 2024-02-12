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
<h3>Reporte de ventas a cliente: {{$cliente->Nombre}}</h3>
<table id="tabla" class="table ">
    <thead>
      <tr>
        <th>#</th>
        <th>Fecha</th>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Monto</th>
        <th>Editar</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($ventas as  $key=>$venta)
        <tr class="fila">
            <td>{{$key+1}}</td>
            <td>{{$venta->created_at}}</td>
            <td>{{$venta->ingreso->producto->Nombre}}</td>
            <td>{{$venta->salida->CantMoldes}}</td>
            <td>{{$venta->salida->Total}} Bs</td>
            <td><a href="{{route("editar_venta",["id"=>$venta->id])}}" class="btn btn-warning">Editar</a></td>
        </tr>
      @endforeach
    </tbody>
  </table>
    <table class="tabla">
        <thead>
          <tr>
            <td>#</td>
            <td>Monto</td>
            <td>Saldo</td>
            <td>Detalle</td>
            <td>Fecha</td>
          </tr>
        </thead>
        <tbody>
          @foreach ($saldos as $key=>$saldo )
              <tr>
                <td>{{$key+1}}</td>
                <td>{{$saldo->Monto}}</td>
                <td>{{$saldo->Saldo}}</td>
                <td>{{$saldo->Detalle}}</td>
                <td>{{$saldo->created_at}}</td>
              </tr>
          @endforeach
        </tbody>
    </table>
  <a href="{{route('reporte_periodo_ventas_pdf',['id'=>$cliente->id,'inicio'=>$inicio,'fin'=>$fin])}}" id="descarga"  class="material-symbols-outlined icono">download</a>    
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
@endsection