@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("registro_lote")}}" class="opciones_head">Registro</a>
<a href="{{route("reporte_lotes")}}" class="opciones_head">Reporte (50)</a>
<a href="{{route("reporte_lotes_total")}}" class="opciones_head">Reporte Total</a>
@endsection
@section("estilos")
@include('components.tablas_css')
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("contenido")
<div class="max-w-6xl mx-auto px-3 py-4">
  <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
    <h3 class="text-xl font-bold text-slate-800">Reporte de lotes <span class="text-sm font-normal text-slate-500">({{$alcance}})</span></h3>
    <a href="{{route('descarga_lotes')}}" class="text-white text-sm font-medium px-4 py-2 rounded-xl" style="background-color:#125149" aria-label="Descargar">Descargar</a>
  </div>

  <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Inversión</p>
      <p class="text-lg font-bold text-slate-800">Bs {{ number_format($kpis['inversion'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Vendido</p>
      <p class="text-lg font-bold text-slate-800">Bs {{ number_format($kpis['vendido'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Ganancia</p>
      <p class="text-lg font-bold {{ $kpis['ganancia'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">Bs {{ number_format($kpis['ganancia'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Stock restante</p>
      <p class="text-lg font-bold text-slate-800">{{ number_format($kpis['stock'], 0, ',', '.') }} uds.</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Lotes</p>
      <p class="text-lg font-bold text-slate-800">{{ $kpis['activos'] }}</p>
      <p class="text-xs text-slate-500">{{ $kpis['pagados'] }} pagados</p>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm mb-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
      <input type="text" id="buscarLote" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="Buscar proveedor...">
      <select id="filtroProducto" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
        <option value="">Todos los productos</option>
        @foreach ($productos as $producto)
          <option value="{{$producto->Nombre}} {{$producto->Tipo}}">{{$producto->Nombre}} {{$producto->Tipo}}</option>
        @endforeach
      </select>
    </div>
    <div class="flex flex-wrap gap-1" id="estadoPills">
      <button type="button" class="estado-pill active text-xs px-3 py-1 rounded-full border border-slate-300" data-estado="todos">Todos</button>
      <button type="button" class="estado-pill text-xs px-3 py-1 rounded-full border border-slate-300" data-estado="con_stock">Con stock</button>
      <button type="button" class="estado-pill text-xs px-3 py-1 rounded-full border border-slate-300" data-estado="agotado">Agotados</button>
      <button type="button" class="estado-pill text-xs px-3 py-1 rounded-full border border-slate-300" data-estado="pagado">Pagados</button>
      <button type="button" class="estado-pill text-xs px-3 py-1 rounded-full border border-slate-300" data-estado="pendiente">Pago pendiente</button>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table id="tabla" class="table w-full text-sm">
        <thead>
          <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
            <th class="px-3 py-2">Lote</th>
            <th class="px-3 py-2">Stock</th>
            <th class="px-3 py-2 text-right">Dinero</th>
            <th class="px-3 py-2">Estado</th>
            <th class="px-3 py-2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($lotes as $lote)
            @php
              $tieneStock = $lote->stock_restante > 0;
              $estado = ($tieneStock ? 'con_stock' : 'agotado') . ' ' . ($lote->Pagado == 1 ? 'pagado' : 'pendiente');
            @endphp
            <tr class="fila border-b border-slate-100" data-estado="{{$estado}}" data-producto="{{$lote->producto->Nombre}} {{$lote->producto->Tipo}}">
              <td class="px-3 py-2" data-order="{{ $lote->created_at->timestamp }}{{ str_pad($lote->id, 10, '0', STR_PAD_LEFT) }}">
                <p class="font-semibold text-slate-800">{{$lote->Proveedor}}</p>
                <p class="text-xs"><span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">{{$lote->producto->Nombre}} {{$lote->producto->Tipo}}</span></p>
                <p class="text-xs text-slate-400">{{$lote->created_at->format('d-m-Y')}}</p>
              </td>
              <td class="px-3 py-2" style="min-width:130px">
                <div class="h-2 rounded-full bg-slate-200 mb-1"><div class="h-2 rounded-full" style="width:{{ min(100, $lote->pct_vendido) }}%;background-color:#125149"></div></div>
                <p class="text-xs text-slate-600">{{$lote->vendidas}} / {{$lote->CantMoldes}} ({{$lote->pct_vendido}}%)</p>
                @if ($lote->producto && $lote->producto->Tipo == 'Por Kilo')
                  <p class="text-xs text-slate-600">Peso: {{ number_format($lote->peso_vendido, 2, ',', '.') }} / {{ number_format($lote->Peso, 2, ',', '.') }} Kg ({{$lote->pct_peso}}%)</p>
                  <p class="text-xs text-slate-500">Restante: {{ number_format($lote->peso_restante, 2, ',', '.') }} Kg</p>
                @endif
                @if ($lote->merma_kg > 0)
                  <p class="text-xs text-amber-700">Merma: {{$lote->merma_kg}} Kg</p>
                @endif
              </td>
              <td class="px-3 py-2 text-right whitespace-nowrap">
                <p class="text-xs text-slate-500">Inv: Bs {{ number_format($lote->costo_total, 2, ',', '.') }}</p>
                <p class="text-xs text-slate-500">Ven: Bs {{ number_format($lote->vendido_total, 2, ',', '.') }}</p>
                <p class="font-bold {{ $lote->ganancia >= 0 ? 'text-emerald-700' : 'text-red-700' }}">Bs {{ number_format($lote->ganancia, 2, ',', '.') }}</p>
              </td>
              <td class="px-3 py-2">
                @if ($lote->Pagado == 1)
                  <span class="text-xs px-2 py-1 rounded-full bg-emerald-100 text-emerald-800 whitespace-nowrap">Pagado</span>
                @else
                  <span class="text-xs px-2 py-1 rounded-full bg-amber-100 text-amber-800 whitespace-nowrap">Pago pendiente</span>
                @endif
              </td>
              <td class="px-3 py-2">
                <div class="flex flex-wrap gap-1">
                  <form action="{{route("reporte_lote_ventas",["id"=>$lote->id])}}" method="get">@csrf<button class="accion-btn bg-amber-500 text-white text-xs px-2 py-1 rounded-lg">Ver ventas</button></form>
                  @if ($lote->Pagado == 0)
                    <form class="PagarLote" action="{{route("pagar_lote",["id"=>$lote->id])}}" method="post" data-monto="Bs {{ number_format($lote->costo_total, 2, ',', '.') }}">@csrf<button class="accion-btn text-white text-xs px-2 py-1 rounded-lg" style="background-color:#125149">Pagar</button></form>
                  @endif
                  <form action="{{route("editar_lote",["id"=>$lote->id])}}" method="get">@csrf<button class="accion-btn bg-slate-200 text-slate-800 text-xs px-2 py-1 rounded-lg">Editar</button></form>
                  <form class="Eliminar" method="POST" action="{{route("eliminar_lote",['id'=>$lote->id])}}">@csrf<button class="accion-btn bg-red-600 text-white text-xs px-2 py-1 rounded-lg">Eliminar</button></form>
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
<style>
  .estado-pill.active { background-color: #125149; color: #fff; }
  .accion-btn { white-space: nowrap; }
</style>
<script>
  var tablaLotes = $('#tabla').DataTable({ dom: 'rtip', order: [[0, 'desc']] });
  $('#buscarLote').on('input', function () { tablaLotes.search(this.value).draw(); });
  $('#filtroProducto').on('change', function () { tablaLotes.column(0).search(this.value).draw(); });
  var filtroEstado = 'todos';
  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    if (settings.nTable !== document.getElementById('tabla') || filtroEstado === 'todos') return true;
    var estados = (tablaLotes.row(dataIndex).node().dataset.estado || '').split(' ');
    return estados.indexOf(filtroEstado) !== -1;
  });
  document.querySelectorAll('#estadoPills .estado-pill').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('#estadoPills .estado-pill').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      filtroEstado = btn.dataset.estado;
      tablaLotes.draw();
    });
  });
  function setupEliminarButtons() {
    $('.Eliminar').off('submit').on('submit', function (e) {
      e.preventDefault();
      var form = this;
      Swal.fire({
        title: '¿Eliminar este lote?',
        text: 'Se borrarán también sus asignaciones. Las ventas ya hechas no se tocan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No'
      }).then((result) => { if (result.isConfirmed) { form.submit(); } });
    });
    $('.PagarLote').off('submit').on('submit', function (e) {
      e.preventDefault();
      var form = this;
      Swal.fire({
        title: 'Confirmar pago al proveedor',
        text: 'Monto total del lote: ' + form.dataset.monto,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#125149',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Pagar',
        cancelButtonText: 'Cancelar'
      }).then((result) => { if (result.isConfirmed) { form.submit(); } });
    });
  }
  $(document).ready(function () {
    setupEliminarButtons();
    tablaLotes.on('responsive-resize responsive-display draw', function () { setupEliminarButtons(); });
  });
</script>
@endsection
