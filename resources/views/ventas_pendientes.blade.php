@extends("header")
@section("titulo", "Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("venta")}}" class="opciones_head">Venta</a>
<a href="{{route("reporte_ventas")}}" class="opciones_head">Reporte</a>
<a href="{{route("ventas_pendientes")}}" class="opciones_head">Pendientes</a>
@endsection
@section("estilos")
@include('components.tablas_css')
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("contenido")
<div class="max-w-6xl mx-auto px-3 py-4">
  <h3 class="text-xl font-bold text-slate-800 mb-4">Ventas pendientes</h3>

  <div class="grid grid-cols-2 gap-3 mb-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Por confirmar</p>
      <p class="text-lg font-bold text-slate-800">{{ $kpis['n'] }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Monto pendiente</p>
      <p class="text-lg font-bold text-slate-800">Bs {{ number_format($kpis['total'], 2, ',', '.') }}</p>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table id="tabla" class="table w-full text-sm">
        <thead>
          <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
            <th class="px-3 py-2">Cliente</th>
            <th class="px-3 py-2 text-right">Moldes</th>
            <th class="px-3 py-2 text-right">Peso</th>
            <th class="px-3 py-2 text-right">Total</th>
            <th class="px-3 py-2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($ventas as $venta )
            <tr class="fila border-b border-slate-100">
              <td class="px-3 py-2 font-semibold text-slate-800">{{$venta->cliente->Nombre ?? 'Sin nombre'}}</td>
              <td class="px-3 py-2 text-right">{{$venta->salida->CantMoldes}}</td>
              <td class="px-3 py-2 text-right">{{ number_format($venta->salida->Peso, 2, ',', '.') }} Kg</td>
              <td class="px-3 py-2 text-right font-semibold whitespace-nowrap">Bs {{ number_format($venta->salida->Total, 2, ',', '.') }}</td>
              <td class="px-3 py-2">
                <div class="flex flex-wrap gap-1">
                  <form action="{{route("modificar",['id'=>$venta->id,'tipo'=>1])}}" method="post">@csrf<button class="text-xs px-2 py-1 rounded-lg text-white" style="background-color:#125149">Confirmar</button></form>
                  <form class="AnularVenta inline" action="{{route("modificar",['id'=>$venta->id,'tipo'=>2])}}" method="post">@csrf<button class="text-xs px-2 py-1 rounded-lg bg-red-600 text-white">Anular</button></form>
                </div>
              </td>
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
  $('.AnularVenta').submit(function (e) {
    e.preventDefault();
    var form = this;
    Swal.fire({
      title: '¿Anular esta pre-venta?',
      text: 'Se devuelve el stock y se elimina el registro.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, anular',
      cancelButtonText: 'No'
    }).then((result) => { if (result.isConfirmed) { form.submit(); } });
  });
</script>
@endsection
