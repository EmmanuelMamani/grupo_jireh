@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("venta")}}" class="opciones_head">Venta</a>
<a href="{{route("reporte_ventas")}}" class="opciones_head">Reporte</a>
<a href="{{route("ventas_pendientes")}}" class="opciones_head">Pendientes</a>
@endsection
@section("estilos")
@include('components.tablas_css')
<script src="https://cdn.tailwindcss.com"></script>
<style>
  .estado-pill.active { background-color: #125149; color: #fff; }
</style>
@endsection
@section("contenido")
<div class="max-w-6xl mx-auto px-3 py-4">
  <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm mb-4">
    <div class="flex flex-wrap items-start justify-between gap-2">
      <div>
        <h3 class="text-xl font-bold text-slate-800">Lote: {{$lote->Proveedor}}</h3>
        <p class="text-sm text-slate-500">{{$lote->producto->Nombre}} {{$lote->producto->Tipo}} · {{$lote->created_at->format('d-m-Y')}} · {{$lote->CantMoldes}} uds.</p>
      </div>
      <a href="{{route('descarga_ventas',['id'=>$id])}}" class="text-white text-sm font-medium px-4 py-2 rounded-xl" style="background-color:#125149" aria-label="Descargar">Descargar</a>
    </div>
  </div>

  <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Vendido</p>
      <p class="text-lg font-bold text-slate-800">Bs {{ number_format($kpis['vendido'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Cobrado</p>
      <p class="text-lg font-bold text-emerald-700">Bs {{ number_format($kpis['cobrado'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Pendiente</p>
      <p class="text-lg font-bold text-amber-700">Bs {{ number_format($kpis['pendiente'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Ganancia</p>
      <p class="text-lg font-bold {{ $kpis['ganancia'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">Bs {{ number_format($kpis['ganancia'], 2, ',', '.') }}</p>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm mb-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
      <input type="text" id="buscarVenta" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="Buscar cliente...">
      <select id="filtroVendedor" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
        <option value="">Todos los vendedores</option>
        @foreach ($vendedores as $vendedor)
          <option value="{{$vendedor->Nombre}}">{{$vendedor->Nombre}}</option>
        @endforeach
      </select>
    </div>
    <div class="flex gap-1" id="pagoPills">
      <button type="button" class="estado-pill active text-xs px-3 py-1 rounded-full border border-slate-300" data-pago="todas">Todas</button>
      <button type="button" class="estado-pill text-xs px-3 py-1 rounded-full border border-slate-300" data-pago="cobrada">Cobradas</button>
      <button type="button" class="estado-pill text-xs px-3 py-1 rounded-full border border-slate-300" data-pago="pendiente">Con saldo</button>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table id="tabla" class="table w-full text-sm">
        <thead>
          <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
            <th class="px-3 py-2">#</th>
            <th class="px-3 py-2">Cliente</th>
            <th class="px-3 py-2">Vendedor</th>
            <th class="px-3 py-2 text-right">Monto</th>
            <th class="px-3 py-2">Fecha</th>
            <th class="px-3 py-2">Estado</th>
            <th class="px-3 py-2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($ventas as $key=>$venta)
            <tr class="fila border-b border-slate-100" data-pago="{{ $venta->pendiente > 0 ? 'pendiente' : 'cobrada' }}">
              <td class="px-3 py-2 text-slate-500">{{$key+1}}</td>
              <td class="px-3 py-2 font-semibold text-slate-800">{{$venta->cliente->Nombre ?? 'Sin nombre'}}</td>
              <td class="px-3 py-2 text-slate-600">{{$venta->user->Nombre}}</td>
              <td class="px-3 py-2 text-right font-semibold whitespace-nowrap">Bs {{ number_format($venta->salida->Total, 2, ',', '.') }}</td>
              <td class="px-3 py-2 text-slate-600 whitespace-nowrap" data-order="{{ $venta->created_at->timestamp }}{{ str_pad($venta->id, 10, '0', STR_PAD_LEFT) }}">{{$venta->created_at->format('d-m-Y')}}</td>
              <td class="px-3 py-2">
                @if ($venta->pendiente > 0)
                  <span class="text-xs px-2 py-1 rounded-full bg-amber-100 text-amber-800 whitespace-nowrap">Debe Bs {{ number_format($venta->pendiente, 2, ',', '.') }}</span>
                @else
                  <span class="text-xs px-2 py-1 rounded-full bg-emerald-100 text-emerald-800 whitespace-nowrap">Cobrada</span>
                @endif
              </td>
              <td class="px-3 py-2">
                <div class="flex flex-wrap gap-1">
                  <a href="{{route('venta_detalle',['id'=>$venta->id])}}" class="text-xs px-2 py-1 rounded-lg bg-slate-200 text-slate-800">Detalle</a>
                  @if (Auth::user()->Rol=="Administrador")
                    <a href="{{route("venta_devolucion",['id'=>$venta->id])}}" class="text-xs px-2 py-1 rounded-lg bg-slate-200 text-slate-800">Devolver</a>
                    <a href="{{route("editar_venta",["id"=>$venta->id])}}" class="text-xs px-2 py-1 rounded-lg bg-amber-500 text-white">Editar</a>
                    <form class="EliminarVenta inline" action="{{route("modificar",['id'=>$venta->id,'tipo'=>2])}}" method="post">@csrf<button class="text-xs px-2 py-1 rounded-lg bg-red-600 text-white">Eliminar</button></form>
                  @endif
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
  var tablaVentas = $('#tabla').DataTable({ dom: 'rtip', order: [[4, 'desc']] });
  $('#buscarVenta').on('input', function () { tablaVentas.search(this.value).draw(); });
  $('#filtroVendedor').on('change', function () { tablaVentas.column(2).search(this.value).draw(); });
  var filtroPago = 'todas';
  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    if (settings.nTable !== document.getElementById('tabla') || filtroPago === 'todas') return true;
    return (tablaVentas.row(dataIndex).node().dataset.pago || '') === filtroPago;
  });
  document.querySelectorAll('#pagoPills .estado-pill').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('#pagoPills .estado-pill').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      filtroPago = btn.dataset.pago;
      tablaVentas.draw();
    });
  });
  $('.EliminarVenta').submit(function (e) {
    e.preventDefault();
    var form = this;
    Swal.fire({
      title: '¿Eliminar esta venta?',
      text: 'Se borrará la venta y su salida. Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'No'
    }).then((result) => { if (result.isConfirmed) { form.submit(); } });
  });
</script>
@endsection
