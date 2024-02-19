@extends("header")
@section("titulo", "Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}"  class="opciones_head">Inicio</a>
<a href="/saldos" class="opciones_head">Cobranza</a>
@if (Auth::user()->Rol=='Administrador')
<a href="/saldo_pasado" class="opciones_head">C. Pasados</a>
@endif
@endsection
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario" method="POST" action="{{route('saldo_pasado')}}">
    @csrf
    <h3>Deudas pasadas</h3>
    <label class="form-label">Zonas:</label>
    <select name="" id="zona" class="form-select" onchange="cambio()">
        @foreach ($zonas as $zona)
            <option value="{{$zona->id}}">{{$zona->Nombre}}</option>
        @endforeach
    </select>
    <label for="" class="form-label">Buscar Cliente:</label>
    <input type="text" id="buscar" class="form-control"><br>
    <label class="form-label">Cliente:</label>
    <select name="cliente" id="cliente" class="form-select">
        <option value="">Seleccionar Cliente</option>
        @foreach ($clientes as $cliente )
            @if ($cliente->zona_id == $zonas->first()->id)
                    <option class="cliente" value="{{$cliente->id}}">{{$cliente->Nombre}}</option>
            @endif
        @endforeach
    </select>
    <Label class="form-label">Monto:</Label>
    <input type="text" class="form-control" name="monto"  value="{{old('monto')}}">
    @if ($errors->has('monto'))
    <span class="error text-danger">{{ $errors->first('monto') }}</span>
    @endif <br>
    <label class="form-label">Motivo de deuda:</label>
    <input type="text" class="form-control" name="motivo"  value="{{old('motivo')}}">
    @if ($errors->has('motivo'))
    <span class="error text-danger">{{ $errors->first('motivo') }}</span>
    @endif <br>
    <div class="row" id="cont_btn">
        <div class="col"><a href="/menu" id="cancelar">Cancelar</a></div>
        <div class="col"><button id="enviar">Registrar</button></div>
    </div>
</form>
<script>
    function cambio(){
        var zona = document.getElementById('zona').value
        var cliente= document.getElementById('cliente')
        cliente.innerHTML="<option >Seleccionar Cliente</option>"
        @foreach ($clientes as $cliente )
            if({{$cliente->zona_id}} == zona){
                cliente.innerHTML+='<option class="cliente" value="{{$cliente->id}}">{{$cliente->Nombre}}</option>'
            }
        @endforeach
    }
</script>
<script>
    $(document).ready(function() {
      $('#buscar').on('input', function() {
        var textoBuscado = $(this).val().toLowerCase(); // Obtener el texto ingresado y convertirlo a minúsculas
        $('.cliente').each(function() {
          var textoOpcion = $(this).text().toLowerCase(); // Obtener el texto de la opción y convertirlo a minúsculas
          if (textoOpcion.includes(textoBuscado)) {
            $(this).show(); // Mostrar la opción si coincide con el texto buscado
          } else {
            $(this).hide(); // Ocultar la opción si no coincide con el texto buscado
          }
        });
      });
    });
  </script>
  <script>
    var carga=document.getElementById("contenedor_carga");
    var enviar=document.getElementById("enviar");
    enviar.onclick=function(){
       carga.style.visibility="visible";
    }
</script>
@endsection