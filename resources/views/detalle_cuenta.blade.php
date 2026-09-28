@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("registro_gasto")}}" class="opciones_head">Registro</a>
<a href="{{route("reporte_diario")}}" class="opciones_head">Reporte</a>
@if (Auth::user()->Rol=='Administrador')
<a href="{{route("reporte_cuenta")}}" class="opciones_head">R. Total</a>
<a href="{{route("cuentas_periodo")}}" class="opciones_head">R. Periodo</a>
<a href="{{route("reporte_historico")}}" class="opciones_head">R Historico</a>
@endif
@endsection
@section("estilos")
@include('components.tablas_css')
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("contenido")
<div class="max-w-6xl mx-auto px-3 py-4">
  <h3 class="text-xl font-bold text-slate-800 mb-1">Detalle de cuentas</h3>
  <p class="text-sm text-slate-500 mb-4">{{$user->Nombre}} · {{date('d-m-Y', strtotime($cuentas->first()->Fecha ?? date('Y-m-d')))}}</p>

  <div class="grid grid-cols-2 gap-3 mb-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Movimientos</p>
      <p class="text-lg font-bold text-slate-800">{{ $cuentas->count() }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Total del día</p>
      <p class="text-lg font-bold text-slate-800">Bs {{ number_format($cuentas->sum('Monto'), 2, ',', '.') }}</p>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table id="tabla" class="table w-full text-sm">
        <thead>
          <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
            <th class="px-3 py-2">Detalle</th>
            <th class="px-3 py-2">Fecha</th>
            <th class="px-3 py-2 text-right">Monto</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($cuentas as $cuenta )
            <tr class="fila border-b border-slate-100">
              <td class="px-3 py-2 text-slate-700">{{$cuenta->Detalle}}</td>
              <td class="px-3 py-2 text-slate-600 whitespace-nowrap">{{date('d-m-Y', strtotime($cuenta->Fecha))}}</td>
              <td class="px-3 py-2 text-right font-semibold whitespace-nowrap">Bs {{ number_format($cuenta->Monto, 2, ',', '.') }}</td>
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
</script>
@endsection
