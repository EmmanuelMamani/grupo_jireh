@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("registro_lote")}}" class="opciones_head">Registro</a>
<a href="{{route("reporte_lotes")}}" class="opciones_head">Reporte</a>
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
  <h3 class="text-xl font-bold text-slate-800 mb-4">Lotes para vender</h3>

  <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Lotes activos</p>
      <p class="text-lg font-bold text-slate-800">{{ number_format($kpis['activos'], 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Stock total</p>
      <p class="text-lg font-bold text-slate-800">{{ number_format($kpis['stock'], 0, ',', '.') }} uds.</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Vendidas</p>
      <p class="text-lg font-bold text-slate-800">{{ number_format($kpis['vendidas'], 0, ',', '.') }} uds.</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Avance</p>
      <p class="text-lg font-bold text-slate-800">{{ $kpis['avance'] }}%</p>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm mb-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
      <input type="text" id="buscarLote" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="Buscar proveedor...">
      <select id="filtroProducto" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm bg-white">
        <option value="">Todos los productos</option>
        @foreach ($productos as $producto)
          <option value="{{$producto->Nombre}} {{$producto->Tipo}}">{{$producto->Nombre}} {{$producto->Tipo}}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table id="tabla" class="table w-full text-sm">
        <thead>
          <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
            <th class="px-3 py-2">Proveedor</th>
            <th class="px-3 py-2">Producto</th>
            <th class="px-3 py-2">Fecha</th>
            <th class="px-3 py-2">Avance</th>
            <th class="px-3 py-2"></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($lotes as $lote)
            @php
              $av = $lote->CantMoldes > 0 ? round($lote->vendidas / $lote->CantMoldes * 100, 1) : 0;
            @endphp
            <tr class="fila border-b border-slate-100">
              <td class="px-3 py-2 font-semibold text-slate-800">{{$lote->Proveedor}}</td>
              <td class="px-3 py-2"><span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-700 whitespace-nowrap">{{$lote->producto->Nombre}} {{$lote->producto->Tipo}}</span></td>
              <td class="px-3 py-2 text-slate-600 whitespace-nowrap" data-order="{{ $lote->created_at->timestamp }}{{ str_pad($lote->id, 10, '0', STR_PAD_LEFT) }}">{{$lote->created_at->format('d-m-Y')}}</td>
              <td class="px-3 py-2" style="min-width:130px">
                <div class="h-2 rounded-full bg-slate-200 mb-1"><div class="h-2 rounded-full" style="width:{{ min(100, $av) }}%;background-color:#125149"></div></div>
                <p class="text-xs text-slate-600">{{$lote->vendidas}} / {{$lote->CantMoldes}} ({{$av}}%)</p>
              </td>
              <td class="px-3 py-2"><form action="{{route("reporte_lote_ventas",["id"=>$lote->id])}}" method="get">@csrf<button class="text-xs px-3 py-1 rounded-lg bg-amber-500 text-white whitespace-nowrap">Ver ventas</button></form></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@include('components.tablas_js')
<script>
  var tablaLotesVender = $('#tabla').DataTable({ dom: 'rtip', order: [[2, 'desc']] });
  $('#buscarLote').on('input', function () { tablaLotesVender.search(this.value).draw(); });
  $('#filtroProducto').on('change', function () { tablaLotesVender.column(1).search(this.value).draw(); });
</script>
@endsection
