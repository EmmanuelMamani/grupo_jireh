@extends("header")
@section("titulo", "Grupo JIREH")
@section("opciones")
<a href="{{route('menu')}}" class="opciones_head">Inicio</a>
<a href="{{route("registro_producto")}}" class="opciones_head">Registro</a>
<a href="{{route("reporte_producto")}}" class="opciones_head">Reporte</a>
@endsection
@section("estilos")
@include('components.tablas_css')
<link rel="stylesheet" href="{{asset("css/reporte.css")}}">
@endsection
@section("contenido")
<h3>Reporte de productos</h3>
<table id="tabla" class="table ">
    <thead>
      <tr>
        <th>#</th>
        <th>Nombre</th>
        <th>Tipo</th>
        <th>Eliminar</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($productos as  $key=>$producto)
        <tr class="fila">
            <td>{{$key+1}}</td>
            <td>{{$producto->Nombre}}</td>
            <td>{{$producto->Tipo}}</td>
            <td><form class="Eliminar" action="{{route("eliminar_producto",["id"=>$producto->id])}}" method="post">@csrf <button class="btn btn-danger">Eliminar</button></form></td>
        </tr>
      @endforeach
    </tbody>
  </table>
  @include('components.tablas_js')
  <script>
         $('#tabla').DataTable();
      $('.Eliminar').submit(function(e){
            e.preventDefault();
            Swal.fire({
            title: '¿Estás seguro que quieres eliminar el producto?',
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