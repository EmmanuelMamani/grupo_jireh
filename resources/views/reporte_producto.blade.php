@extends("header")
@section("titulo", "Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("registro_producto")}}" class="opciones_head">Registro</a>
<a href="{{route("reporte_producto")}}" class="opciones_head">Reporte</a>
@endsection
@section("estilos")
@include('components.tablas_css')
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("contenido")
<div class="max-w-6xl mx-auto px-3 py-4">
  <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
    <h3 class="text-xl font-bold text-slate-800">Reporte de productos</h3>
    <a href="{{route('registro_producto')}}" class="text-white text-sm font-medium px-4 py-2 rounded-xl" style="background-color:#DA7922">+ Nuevo producto</a>
  </div>
  <div class="grid grid-cols-2 gap-3 mb-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Productos activos</p>
      <p class="text-lg font-bold text-slate-800">{{ $productos->count() }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Lotes registrados</p>
      <p class="text-lg font-bold text-slate-800">{{ number_format($productos->sum('ingresos_count'), 0, ',', '.') }}</p>
    </div>
  </div>
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table id="tabla" class="table w-full text-sm">
        <thead>
          <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
            <th class="px-3 py-2">Nombre</th>
            <th class="px-3 py-2">Tipo</th>
            <th class="px-3 py-2 text-right">Lotes</th>
            <th class="px-3 py-2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($productos as $producto)
            <tr class="fila border-b border-slate-100">
              <td class="px-3 py-2 font-semibold text-slate-800">{{$producto->Nombre}}</td>
              <td class="px-3 py-2"><span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-700 whitespace-nowrap">{{$producto->Tipo}}</span></td>
              <td class="px-3 py-2 text-right">{{ number_format($producto->ingresos_count, 0, ',', '.') }}</td>
              <td class="px-3 py-2"><form class="Eliminar inline" action="{{route("eliminar_producto",["id"=>$producto->id])}}" method="post">@csrf<button class="text-xs px-2 py-1 rounded-lg bg-red-600 text-white">Eliminar</button></form></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@include('components.tablas_js')
<script>
  $('#tabla').DataTable();
  $('.Eliminar').submit(function(e){
    e.preventDefault();
    var form = this;
    Swal.fire({
      title: '¿Estás seguro que quieres eliminar el producto?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí',
      cancelButtonText: 'No'
    }).then((result) => { if (result.isConfirmed) { form.submit(); } });
  });
</script>
@endsection
