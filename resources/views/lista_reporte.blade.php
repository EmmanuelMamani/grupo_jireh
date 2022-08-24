@extends("header")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("lista_reporte")}}" class="opciones_head">Lista</a>
<a href="{{route("registro_lista")}}" class="opciones_head">Registro</a>
@endsection
@section("estilos")
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="{{asset("css/reporte.css")}}">
@endsection
@section("titulo","Grupo JIREH")
@section("contenido")
<h3>Lista</h3>
<table id="tabla" class="table ">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Producto</th>
        <th>Unidades</th>
        <th>Cancelar</th>
        <th>Completar</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($listas as $lista )
        <tr class="fila">
          <td>{{$lista->cliente->Nombre}}</td>
          <td>{{$lista->producto->Nombre}}</td>
          <td>{{$lista->Unidades}}</td>
<<<<<<< HEAD
          <td><a href="{{route("cancelar_lista",['id'=>$lista->id])}}" class="btn btn-danger">Cancelar</a></td>
          <td><a>Completar</a></td>
=======
          <td>
            <form class="Eliminar" action="{{route("cancelar_lista",['id'=>$lista->id])}}" method="POST">
              @csrf
              <button>Cancelar</button>
            </form>
          </td>
          <td><a href="{{route("completar_lista",['id'=>$lista->id])}}">Completar</a></td>
>>>>>>> d87cd779a0407441b92283d85b9b3cb586c44821
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
<script>
  $('.Eliminar').submit(function(e){
            e.preventDefault();
            Swal.fire({
            title: '¿Estás seguro que quieres este registro?',
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