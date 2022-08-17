@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form action="" method="post" id="formulario">
    <h3>Registrar pedido</h3>
    <label>Zona:</label>
    <select name="zona" class="form-select" id="zona">
      <option>Elije una zona</option>
      @foreach ($zonas as $zona)
        <option value="{{$zona->id}}">{{$zona->Nombre}}</option>
      @endforeach
    </select>
    <label>Cliente:</label>
    <select name="cliente" class="form-select" id="cliente">
      <option>Elije un cliente</option>
      @foreach ($clientes as $cliente )
        <option value="{{$cliente->id}}">{{$cliente->Nombre}}</option>
      @endforeach
    </select>
    <label>producto</label>
    <select name="producto" class="form-select">
      <option>Elije un producto</option>
      @foreach ($productos as $producto )
        <option value="{{$producto->id}}">{{$producto->Nombre}}</option>
      @endforeach
    </select>
    <label>Unidades:</label>
    <input type="text" class="form-control" name="uninades">
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
              cliente.innerHTML+="<option value='{{$cliente->id}}'>{{$cliente->Nombre}}</option>";
          }
      @endforeach
  });
</script>
@endsection