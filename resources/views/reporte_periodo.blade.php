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
<h3 class="text-xl font-bold text-slate-800 mb-4">Reporte de cuentas {{$titulo}}</h3>
<div class="grid grid-cols-3 gap-3 mb-4">
  <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
    <p class="text-xs text-slate-500">Total período</p>
    <p class="text-lg font-bold text-slate-800">Bs {{ number_format($monto, 2, ',', '.') }}</p>
  </div>
  <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
    <p class="text-xs text-slate-500">Días</p>
    <p class="text-lg font-bold text-slate-800">{{ $kpis['dias'] }}</p>
  </div>
  <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
    <p class="text-xs text-slate-500">Empleados</p>
    <p class="text-lg font-bold text-slate-800">{{ $kpis['empleados'] }}</p>
  </div>
</div>
@include('components.categorias_totales')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table id="tabla" class="table w-full text-sm">
    <thead>
      <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
        <th class="px-3 py-2">Nombre</th>
        <th class="px-3 py-2">Fecha</th>
        <th class="px-3 py-2 text-right">Monto</th>
        <th class="px-3 py-2">Detalle</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($cuentas as $cuenta )
        @php($usuario = $usuarios[$cuenta->user_id] ?? null)
        @if ($usuario)
            <tr class="fila border-b border-slate-100">
              <td class="px-3 py-2 font-semibold text-slate-800">{{$usuario->Nombre}}</td>
              <td class="px-3 py-2 text-slate-600 whitespace-nowrap">{{date('d-m-Y', strtotime($cuenta->Fecha));}}</td>
              <td class="px-3 py-2 text-right font-semibold whitespace-nowrap">Bs {{ number_format($cuenta->monto, 2, ',', '.') }}</td>
              <td class="px-3 py-2"><form action="{{route("detalle_cuenta",['id'=>$usuario->id,'fecha'=>$cuenta->Fecha])}}" method="GET">@csrf<button class="text-xs px-2 py-1 rounded-lg bg-slate-200 text-slate-800">Detalle</button></form></td>
            </tr>
        @endif
      @endforeach
    </tbody>
  </table>
</div>
</div>
<div class="flex justify-end mt-3">
  <a href="{{route('descarga_periodo',['inicio'=>$inicio,'fin'=>$fin])}}" class="text-white text-sm font-medium px-4 py-2 rounded-xl" style="background-color:#125149" aria-label="Descargar">Descargar</a>
</div>
</div>
  @include('components.tablas_js')
  <script>
         $('#tabla').DataTable();
</script>
@endsection