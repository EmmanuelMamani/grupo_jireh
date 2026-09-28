@extends("header")
@section("titulo", "Grupo JIREH")
@section("opciones")
<a href="{{route('menu')}}" class="opciones_head">Inicio</a>
<a href="{{route('registro_cliente')}}" class="opciones_head">Registro</a>
<a href="{{route('reporte_cliente')}}" class="opciones_head">Reporte</a>
@if (Auth::user()->Rol=='Administrador')

@endif
@endsection
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario" method="POST" action="{{route('registro_cliente')}}">
    @csrf
    <h3>Registro de cliente</h3>
    <label class="form-label">Nombre completo:</label>
    <input type="text" name="nombre"  class="form-control"  value="{{old('nombre')}}">
    @if ($errors->has('nombre'))
    <span class="error text-danger">{{ $errors->first('nombre') }}</span>
    @endif <br>
    <label class="form-label">Telefono:</label>
    <input type="text" name="telefono"  class="form-control"  value="{{old('telefono')}}">
    @if ($errors->has('telefono'))
    <span class="error text-danger">{{ $errors->first('telefono') }}</span>
    @endif <br>
    <label class="form-label">Direccion:</label>
    <input type="text" name="direccion"  class="form-control"  value="{{old('direccion')}}">
    @if ($errors->has('direccion'))
    <span class="error text-danger">{{ $errors->first('direccion') }}</span>
    @endif <br>
    <label class="form-label">Zona:</label>
    <select name="zona" id="zona" class="form-select">
        @foreach ($zonas as $zona )
            <option value="{{$zona->id}}">{{$zona->Nombre}}</option>
        @endforeach
    </select>
    <div class="row" id="cont_btn">
        <div class="col"><a href="{{route('menu')}}" id="cancelar">Cancelar</a></div>
        <div class="col"><button id="enviar">Registrar</button></div>
    </div>
</form>
@if (session('registrar') == 'ok')
<div class="row mt-2" id="post_acciones">
    <div class="col"><button type="button" id="otro_cliente" class="btn btn-secondary w-100">Registrar otro</button></div>
    @if (Auth::user()->Rol == 'Administrador')
    <div class="col"><a href="{{route('reporte_cliente')}}" class="btn w-100 text-white" style="background-color:#125149">Ver reporte</a></div>
    @endif
</div>
<script>
    (function(){
        var nombre = document.querySelector('input[name="nombre"]');
        var otro = document.getElementById('otro_cliente');
        if (otro && nombre) {
            otro.addEventListener('click', function(){
                nombre.focus();
                nombre.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        }
    })();
</script>
@endif
@endsection