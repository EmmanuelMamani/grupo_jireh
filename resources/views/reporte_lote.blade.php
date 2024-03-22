@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">inicio</a>
<a href="{{route("registro_lote")}}" class="opciones_head">Registro</a>
<a href="{{route("reporte_lotes")}}" class="opciones_head">Reporte</a>
@endsection
@section("estilos")
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
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
        <th>Peso total</th>
        <th>Precio unitario</th>
        <th>Precio total</th>
        <th>Ganancia de ventas</th>
        <th>Unidades vendidas</th>
        <th>Merma</th>
        <th>Ventas</th>
        <th>Pagar</th>
        <th>Eliminar</th>
        <th>Editar</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($lotes as $key=>$lote)
        <tr class="fila">
          <td>{{$key+1}}</td>
          <td>{{$lote->Proveedor}}</td>
          <td>{{$lote->producto->Nombre}} {{$lote->producto->Tipo}}</td>
          <td>{{$lote->created_at->format('Y-m-d')}}</td>
          <td>{{$lote->CantMoldes}}</td>
          <td>{{$lote->Peso}} Kg</td>
          <td>{{$lote->Precio}} Bs</td>
          @if($lote->producto->Tipo=="Por Kilo")
            <td>{{$lote->Peso * $lote->Precio}} Bs</td>
            <td>{{$lote->salidas->sum('Total')-$lote->Peso * $lote->Precio}} Bs</td>
          @else
            <td>{{$lote->CantMoldes * $lote->Precio}} Bs</td>
            <td>{{$lote->salidas->sum('Total')-$lote->CantMoldes * $lote->Precio}} Bs</td>
          @endif
          <td>{{$lote->salidas->sum('CantMoldes')}}</td>
          @if ($lote->producto->Tipo!="Por Kilo" || $lote->CantMoldes - $lote->salidas->sum('CantMoldes')>0)
          <td>Sin merma</td>
          @else
          <td>{{$lote->salidas->sum('Peso')}} Kg</td>
          @endif
          <td><form action="{{route("reporte_lote_ventas",["id"=>$lote->id])}}" method="get">@csrf <button class="btn btn-warning">Ver Ventas</button></form></td>
          @if ($lote->Pagado == 0)
          <td> <form action="{{route("pagar_lote",["id"=>$lote->id])}}" method="post"> @csrf <button class="btn btn-success">Pagar</button></form></td>
          @else
           <td>Pagado</td> 
          @endif
          <td>
            <form class="Eliminar" method="POST"  action="{{route("eliminar_lote",['id'=>$lote->id])}}">@csrf <button class="btn btn-danger"> Eliminar</button></form>
          </td>
          <td><form action="{{route("editar_lote",["id"=>$lote->id])}}" method="get">@csrf <button class="btn btn-warning">Editar</button></form></td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <a href="{{route('descarga_lotes')}}" id="descarga"  class="material-symbols-outlined icono">download</a>
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
      $('.Eliminar').submit(function(e){
            e.preventDefault();
            Swal.fire({
            title: '¿Estás seguro que quieres eliminar el lote?',
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