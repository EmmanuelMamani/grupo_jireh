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
<link rel="stylesheet" href="{{asset("css/reporte.css")}}">
@include('components.tablas_css')
@endsection
@section("contenido")
<h3>Detalle de cuentas</h3>
<table id="tabla" class="table ">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Monto</th>
        <th>Detalle</th>
        <th>Fecha</th>
      </tr>
    </thead>
    <tbody>
       @foreach ($cuentas as $cuenta )
            <tr class="fila">
                <td>{{$user->Nombre}}</td>
                <td>{{$cuenta->Monto}}</td>
                <td>{{$cuenta->Detalle}}</td>
                <td>{{date('d-m-Y', strtotime($cuenta->Fecha));}}</td>
            </tr>
        @endforeach 
    </tbody>
  </table>
  <a href="{{route('descarga_diario',['user_id'=>$user->id])}}" id="descarga" aria-label="Descargar"><x-icon name="download" class="icono"/></a>
  @include('components.tablas_js')
  <script>
         $('#tabla').DataTable();
</script>
@endsection