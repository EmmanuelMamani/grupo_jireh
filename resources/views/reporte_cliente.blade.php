@extends("header")
@section("titulo", "Grupo JIREH")
@section("opciones")
<a href="{{route('menu')}}" class="opciones_head">Inicio</a>
<a href="{{route('registro_cliente')}}" class="opciones_head">Registro</a>
<a href="{{route('reporte_cliente')}}" class="opciones_head">Reporte</a>
@endsection
@section("estilos")
@include('components.tablas_css')
<script src="https://cdn.tailwindcss.com"></script>
<style>
  .kpi-top-green { border-top: 4px solid #125149; }
  .kpi-top-orange { border-top: 4px solid #DA7922; }
  .zona-pill.active { background-color: #125149; color: #fff; }
  .deuda-pill.active { background-color: #DA7922; color: #fff; }
  .accion-btn { font-size: .75rem; padding: .25rem .5rem; border-radius: .5rem; white-space: nowrap; }
</style>
@endsection
@section("contenido")
<div class="max-w-6xl mx-auto px-3 py-4">
  <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
    <h3 class="text-xl font-bold text-slate-800">Reporte de clientes</h3>
    <a href="{{route('registro_cliente')}}" class="text-white text-sm font-medium px-4 py-2 rounded-xl" style="background-color:#DA7922">+ Nuevo cliente</a>
  </div>

  <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm kpi-top-green">
      <p class="text-xs text-slate-500">Por cobrar total</p>
      <p class="text-lg font-bold text-slate-800">Bs {{ number_format($kpis['total'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm kpi-top-orange">
      <p class="text-xs text-slate-500">Clientes con deuda</p>
      <p class="text-lg font-bold text-slate-800">{{ $kpis['con_deuda'] }} / {{ count($clientes) }}</p>
      <p class="text-xs text-slate-500">{{ $kpis['sin_deuda'] }} sin deuda</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm kpi-top-green">
      <p class="text-xs text-slate-500">Ticket promedio</p>
      <p class="text-lg font-bold text-slate-800">Bs {{ number_format($kpis['ticket'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm kpi-top-orange">
      <p class="text-xs text-slate-500">Zona top deudora</p>
      <p class="text-lg font-bold text-slate-800">{{ $kpis['top_zona'] }}</p>
      <p class="text-xs text-slate-500">Bs {{ number_format($kpis['top_zona_monto'], 2, ',', '.') }}</p>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm mb-4">
    <input type="text" id="buscarCliente" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm mb-2" placeholder="Buscar por nombre o teléfono...">
    <div class="flex flex-wrap gap-1 mb-2" id="zonaPills">
      <button type="button" class="zona-pill active text-xs px-3 py-1 rounded-full border border-slate-300" data-zona="">Todas</button>
      @foreach ($zonas as $zona)
        <button type="button" class="zona-pill text-xs px-3 py-1 rounded-full border border-slate-300" data-zona="{{$zona->Nombre}}">{{$zona->Nombre}}</button>
      @endforeach
    </div>
    <div class="flex gap-1" id="deudaPills">
      <button type="button" class="deuda-pill active text-xs px-3 py-1 rounded-full border border-slate-300" data-deuda="todos">Todos</button>
      <button type="button" class="deuda-pill text-xs px-3 py-1 rounded-full border border-slate-300" data-deuda="con">Con deuda</button>
      <button type="button" class="deuda-pill text-xs px-3 py-1 rounded-full border border-slate-300" data-deuda="sin">Sin deuda</button>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table id="tabla" class="table w-full text-sm">
        <thead>
          <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
            <th class="px-3 py-2">Cliente</th>
            <th class="px-3 py-2">Zona</th>
            <th class="px-3 py-2 text-right">Deuda</th>
            <th class="px-3 py-2">Tienda</th>
            <th class="px-3 py-2">Ubic.</th>
            <th class="px-3 py-2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($clientes as $cliente)
            @php
              $tel = preg_replace('/\D/', '', (string) $cliente->Telefono);
              if (strlen($tel) == 8) $tel = '591' . $tel;
              $deuda = (float) $cliente->deuda_actual;
            @endphp
            <tr class="fila border-b border-slate-100" data-deuda="{{$deuda}}">
              <td class="px-3 py-2">
                <p class="font-semibold text-slate-800">{{$cliente->Nombre}}</p>
                <p class="text-xs whitespace-nowrap">
                  <a href="tel:+{{$tel}}" class="text-slate-500">{{$cliente->Telefono}}</a>
                  <a href="https://wa.me/{{$tel}}" target="_blank" rel="noopener" class="ml-2 text-emerald-700 font-semibold">WhatsApp</a>
                </p>
              </td>
              <td class="px-3 py-2"><span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-700 whitespace-nowrap">{{$cliente->zona->Nombre ?? '—'}}</span></td>
              <td class="px-3 py-2 text-right font-semibold {{ $deuda > 0 ? 'text-amber-700' : 'text-slate-400' }}">
                @if ($deuda > 0) Bs {{ number_format($deuda, 2, ',', '.') }} @else Sin deuda @endif
              </td>
              <td class="px-3 py-2">
                @if ($cliente->getTienda == 'no')
                  <span class="text-xs text-slate-400">—</span>
                @else
                  <a href="{{route('ver_tienda', ['id' => $cliente->id])}}" class="text-xs font-semibold text-slate-700 underline">Ver foto</a>
                @endif
              </td>
              <td class="px-3 py-2">
                @if ($cliente->direccion_map == NULL)
                  <span class="text-xs text-slate-400">—</span>
                @else
                  <a href="{{$cliente->direccion_map}}" target="_blank" rel="noopener" class="text-xs font-semibold text-slate-700 underline">Mapa</a>
                @endif
              </td>
              <td class="px-3 py-2">
                <div class="flex flex-wrap gap-1">
                  <a href="{{route('ventas_periodo', ['id' => $cliente->id])}}" class="accion-btn bg-amber-500 text-white">Kardex</a>
                  <a href="{{route('saldos')}}?cliente={{$cliente->id}}&zona={{$cliente->zona_id}}" class="accion-btn text-white" style="background-color:#125149">Cobrar</a>
                  <a href="{{route('editar_cliente', ['id' => $cliente->id])}}" class="accion-btn bg-slate-200 text-slate-800">Editar</a>
                  @if (Auth::user()->Rol == 'Administrador')
                    <form class="Eliminar1 inline" action="{{route('eliminar_cliente', ['id' => $cliente->id])}}" method="post">@csrf<button class="accion-btn bg-red-600 text-white">Eliminar</button></form>
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <h4 class="font-semibold text-slate-800 mt-4 mb-2">Saldos por zona</h4>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
    @foreach ($zonas as $zona)
      <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
        <p class="text-xs text-slate-500">{{$zona->Nombre}}</p>
        <p class="text-base font-bold text-slate-800">Bs {{ number_format($zona_saldo[$zona->id] ?? 0, 2, ',', '.') }}</p>
      </div>
    @endforeach
  </div>
</div>
@include('components.tablas_js')
<script>
  var tablaClientes = $('#tabla').DataTable({ dom: 'rtip' });
  $('#buscarCliente').on('input', function () { tablaClientes.search(this.value).draw(); });
  document.querySelectorAll('#zonaPills .zona-pill').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('#zonaPills .zona-pill').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      tablaClientes.column(1).search(btn.dataset.zona).draw();
    });
  });
  var filtroDeuda = 'todos';
  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    if (settings.nTable !== document.getElementById('tabla') || filtroDeuda === 'todos') return true;
    var deuda = parseFloat(tablaClientes.row(dataIndex).node().dataset.deuda || 0);
    return filtroDeuda === 'con' ? deuda > 0 : deuda <= 0;
  });
  document.querySelectorAll('#deudaPills .deuda-pill').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('#deudaPills .deuda-pill').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      filtroDeuda = btn.dataset.deuda;
      tablaClientes.draw();
    });
  });
  $('.Eliminar1').submit(function (e) {
    e.preventDefault();
    var form = this;
    Swal.fire({
      title: '¿Estás seguro que quieres eliminar el cliente?',
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
