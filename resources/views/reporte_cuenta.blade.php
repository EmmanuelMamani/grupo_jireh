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
<link rel="stylesheet" href="{{asset("css/reporte.css")}}">
@endsection
@section("contenido")
<h3>Reporte de cuentas {{$titulo}}</h3>
<table id="tabla" class="table ">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Fecha</th>
        <th>Monto</th>
        <th>Detalle</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($cuentas as $cuenta )
        @php($usuario = $usuarios[$cuenta->user_id] ?? null)
        @if ($usuario)
            <tr class="fila">
              <td>{{$usuario->Nombre}}</td>
              <td>{{ \DateTime::createFromFormat('Y-m-d', $cuenta->Fecha)->format('d-m-Y') }}</td>
              <td>{{$cuenta->monto}}</td>
              <th><form action="{{route("detalle_cuenta",['id'=>$usuario->id,'fecha'=>$cuenta->Fecha])}}" method="GET">@csrf <button class="btn btn-secondary">Detalle</button></form></th>
            </tr>
        @endif
      @endforeach
    </tbody>
  </table>
  @if ($titulo=="Historico")
    <a href="{{route('descarga_cuentas')}}" id="descarga" aria-label="Descargar"><x-icon name="download" class="icono"/></a>
  @else
    <a href="{{route('descarga_cuentas_diarias')}}" id="descarga" aria-label="Descargar"><x-icon name="download" class="icono"/></a>
  @endif
  @include('components.tablas_js')
  <script>
         $('#tabla').DataTable();
</script>
@endsection