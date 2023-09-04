@extends("header")
@section("titulo", "Grupo JIREH")
@section("opciones")
<a href="/menu" class="opciones_head">Inicio</a>
<a href="/registro_cliente" class="opciones_head">Registro</a>
@if (Auth::user()->Rol=='Administrador')
<a href="/reporte_cliente" class="opciones_head">Reporte</a>
@endif
@endsection
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario" method="POST" action="{{route('editar_cliente',['id'=>$cliente->id])}}" enctype="multipart/form-data">
    @csrf
    <h3>Editar cliente</h3>
    <label class="form-label">Nombre completo:</label>
    <input type="text" name="nombre"  class="form-control"  value="{{$cliente->Nombre}}">
    @if ($errors->has('nombre'))
    <span class="error text-danger">{{ $errors->first('nombre') }}</span>
    @endif <br>
    <label class="form-label">Telefono:</label>
    <input type="text" name="telefono"  class="form-control"  value="{{$cliente->Telefono}}" readonly>
    @if ($errors->has('telefono'))
    <span class="error text-danger">{{ $errors->first('telefono') }}</span>
    @endif <br>
    <label class="form-label">Direccion:</label>
    <input type="text" name="direccion"  class="form-control"  value="{{$cliente->Direccion}}">
    @if ($errors->has('direccion'))
    <span class="error text-danger">{{ $errors->first('direccion') }}</span>
    @endif <br>
    <label class="form-label">Zona:</label>
    <select name="zona" id="zona" class="form-select">
        @foreach ($zonas as $zona )
            @if ($zona->id == $cliente->zona_id)
            <option value="{{$zona->id}}" selected>{{$zona->Nombre}}</option>
            @else
            <option value="{{$zona->id}}">{{$zona->Nombre}}</option>
            @endif
        @endforeach
    </select>
    <label class="form-label">Mapa:</label>
    <input type="text" name="mapa"  class="form-control">
    <label class="form-label">Tienda:</label>
    <input type="file" name="tienda" class="form-control">
    <div class="row" id="cont_btn">
        <div class="col"><a href="/menu" id="cancelar">Cancelar</a></div>
        <div class="col"><button id="enviar">Registrar</button></div>
    </div>
</form>
@endsection