@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("lista_reporte")}}" class="opciones_head">Lista</a>
<a href="{{route("registro_lista")}}" class="opciones_head">Registro</a>
@endsection
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form action="{{route("registro_lista")}}" method="post" id="formulario">
  @csrf
    <h3>Registrar pedido</h3>
    <label>Zona:</label>
    <select name="zona" class="form-select" id="zona">
      <option>Elije una zona</option>
      @foreach ($zonas as $zona)
        <option value="{{$zona->id}}">{{$zona->Nombre}}</option>
      @endforeach
    </select>
    <label for="" class="form-label">Buscar Cliente:</label>
    <input type="text" id="buscar" class="form-control"><br>
    <label>Cliente:</label>
    <select name="cliente" class="form-select" id="cliente">
      <option>Elije un cliente</option>
      @foreach ($clientes as $cliente )
        <option class="cliente" value="{{$cliente->id}}" @if(old('cliente') == $cliente->id ) selected @endif>{{$cliente->Nombre}}</option>
      @endforeach
    </select>
    @if ($errors->has('cliente'))
    <span class="error text-danger" for="cliente">{{ $errors->first('cliente') }}</span><br>
    @endif  
    <label>producto</label>
    <select name="producto" class="form-select">
      <option>Elije un producto</option>
      @foreach ($productos as $producto )
        <option value="{{$producto->id}}" @if(old('producto') == $producto->id ) selected @endif>{{$producto->Nombre}}</option>
      @endforeach
    </select>
    @if ($errors->has('producto'))
    <span class="error text-danger" for="cliente">{{ $errors->first('producto') }}</span><br>
  @endif  
    <label>Unidades:</label>
    <input type="text" class="form-control" name="unidades" value="{{old("unidades")}}">
    @if ($errors->has('unidades'))
    <span class="error text-danger" for="cliente">{{ $errors->first('unidades') }}</span><br>
  @endif  
    <div class="row" id="cont_btn">
        <div class="col"><a href="menu" id="cancelar">Cancelar</a></div>
        <div class="col"><button id="enviar">Agregar</button></div>
    </div>
</form>
<script>
  var zona=document.getElementById("zona");
  zona.addEventListener('change',(event)=>{
      var zona_id=zona.options[zona.selectedIndex].value;
      var cliente=document.getElementById("cliente");
      cliente.innerHTML="<option>Elije un cliente</option>";
      @foreach ($clientes as $cliente)
          if(zona_id=={{$cliente->zona_id}}){
              cliente.innerHTML+="<option class='cliente' value='{{$cliente->id}}'>{{$cliente->Nombre}}</option>";
          }
      @endforeach
  });
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
@endsection