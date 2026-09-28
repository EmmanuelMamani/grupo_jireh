@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("venta")}}" class="opciones_head">Venta</a>
<a href="{{route("reporte_ventas")}}" class="opciones_head">Reporte</a> 
<a href="{{route("ventas_pendientes")}}" class="opciones_head">Pendientes</a>
@endsection
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@push('head-scripts')
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
@endpush
@section("contenido")
<form action="{{route('venta')}}" id="formulario" method="POST">
    <h3>Pre-Venta</h3>
    @csrf
    <label class="form-label">Zona:</label>
    <select name="zona" id="zona" class="form-select">

        <option>Elige la zona</option>
        @foreach ($zonas as $zona)
            <option value="{{$zona->id}}" @if(old('zona') == $zona->id ) selected @endif  > {{$zona->Nombre}}</option>
        @endforeach
        
    </select>
    @if ($errors->has('zona'))
               <span class="error text-danger" for="zona">{{ $errors->first('zona') }}</span><br>
    @endif  
    <label class="form-label" for="cliente">Cliente:</label>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" value="0" id="tipo" name="tipo">
        <label class="form-check-label" for="tipo">
          Redondear total a entero
        </label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="contado" name="contado" value="0">
        <label class="form-check-label">
          Al contado
        </label>
    </div>
    <label for="buscar" class="form-label">Buscar Cliente:</label>
    <input type="text" id="buscar" class="form-control"><br>
    <select name="cliente" id="cliente" class="form-select">
        <option >Elige un cliente</option>
        @foreach ($clientes as $cliente)
            <option class="cliente" value="{{$cliente->id}}" @if(old('cliente') == $cliente->id ) selected @endif>{{$cliente->Nombre}}</option>
        @endforeach
    </select>
    @if ($errors->has('cliente'))
               <span class="error text-danger" for="cliente">{{ $errors->first('cliente') }}</span><br>
    @endif  
        <script>
            var zona=document.getElementById("zona");
            zona.addEventListener('change',(event)=>{
                var zona_id=zona.options[zona.selectedIndex].value;
                var zona_text=zona.options[zona.selectedIndex].text;
                var cliente=document.getElementById("cliente");
                cliente.innerHTML="<option>Elige un cliente</option>";
                @foreach ($clientes as $cliente)
                    if(zona_id=={{$cliente->zona_id}}){
                        cliente.innerHTML+="<option class='cliente' value='{{$cliente->id}}'>{{$cliente->Nombre}}</option>";
                    }
                    if(zona_text=="Elige la zona"){
                        cliente.innerHTML+="<option class='cliente' value='{{$cliente->id}}'>{{$cliente->Nombre}}</option>"; 
                    }
                @endforeach
            });
        </script>
    <label class="form-label" for="producto">Producto:</label>
    <select name="producto" id="producto" class="form-select">
        <option >Elige un producto</option>
        @foreach ($productos as $producto)
            <option value="{{$producto->id}}" @if(old('producto') == $producto->id ) selected @endif>{{$producto->Nombre}} {{$producto->Tipo}}</option>
        @endforeach
    </select>
    @if ($errors->has('producto'))
               <span class="error text-danger" for="producto">{{ $errors->first('producto') }}</span><br>
    @endif  
    <label class="form-label" for="lote">Lote:</label>
    <select name="lote" id="lote" class="form-select">
        <option >Elige un lote</option>
    </select>
    
    @if ($errors->has('lote'))
               <span class="error text-danger" for="lote">{{ $errors->first('lote') }}</span><br>
    @endif  
    <label for="lote" id="cantidad_lotes"></label><br>
    @if(old('lote')!=null)
    <script>
        var producto_id=producto.options[producto.selectedIndex].value;
        var label=document.getElementById("cantidad_lotes");
        var lote=document.getElementById("lote");
        lote.innerHTML="<option>Elige un lote</option>";
        @foreach ($lotes as $lote)
                
                if(producto_id=={{$lote->ingreso->producto_id}} &&  {{$lote->ingreso->Activo}} == 1){
                    lote.innerHTML=lote.innerHTML+"<option value='{{$lote->ingreso->id}}' @if(old('lote') == $lote->ingreso->id) selected @endif>{{$lote->ingreso->Proveedor}} - {{$lote->ingreso->CantMoldes}} - {{$lote->ingreso->created_at->format('Y-m-d')}}</option>";
                    if({{old('lote')}} == {{$lote->ingreso->id}}){
                        label.innerHTML="Cantidad restante en el lote: {{$lote->CantMoldes}}";
                    }
                }
        @endforeach
        
    </script>
       
    @endif
    <script>
        //------------------------------------------------------
        var producto=document.getElementById("producto");
        producto.addEventListener('change',(event)=>{
            var producto_text=producto.options[producto.selectedIndex].text;
           
                var label=document.getElementById("cantidad_lotes");
                label.innerHTML="";
            
            //-------------------------------------------
            producto_text=producto_text.split(" ");
            var unidad=producto_text[producto_text.length-1];
            var label=document.getElementById("cantidad_lotes");
            var peso=document.getElementById("peso");
            if(unidad=="Unidad"){
                peso.disabled=true;
            }else{
                peso.disabled=false;
            }
            //--------------------------------------------
            var producto_id=producto.options[producto.selectedIndex].value;
            var lote=document.getElementById("lote");
                lote.innerHTML="<option>Elige un lote</option>";
                @foreach ($lotes as $lote)
                    if(producto_id=={{$lote->ingreso->producto_id}} &&  {{$lote->ingreso->Activo}} == 1){
                        lote.innerHTML+="<option value='{{$lote->ingreso->id}}'>{{$lote->ingreso->Proveedor}} - {{$lote->ingreso->CantMoldes}} - {{$lote->ingreso->created_at->format('Y-m-d')}}</option>";
                    }
                @endforeach
        });
        //------------------------------------------------------
        var lote_elegido=document.getElementById("lote");
        lote_elegido.addEventListener('change',(event)=>{
            var label=document.getElementById("cantidad_lotes");
            var id_lote=lote_elegido.options[lote_elegido.selectedIndex].value;
            var texto=lote_elegido.options[lote_elegido.selectedIndex].text;
            @foreach ($lotes as $lote)
                if({{$lote->ingreso->id}}==id_lote){
                    label.innerHTML="Cantidad restante en el lote: {{$lote->CantMoldes}}";
                }
            @endforeach
            if(texto== "Elige un lote"){
                label.innerHTML="";
            }
        });


    </script>
    <div class="row">
        <div class="col">
            <label class="form-label" for="cantidad_moldes">Cantidad de moldes:</label>
        </div>

        <div class="col">
            <label class="form-label" for="peso">Peso total:</label>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <input type="text" name="cantidad_moldes" id="cantidad_moldes" class="form-control" inputmode="numeric" autocomplete="off" value="{{old('cantidad_moldes')}}">
            @if ($errors->has('cantidad_moldes'))
               <span class="error text-danger" for="cantidad_moldes">{{ $errors->first('cantidad_moldes') }}</span>
            @endif  
        </div>
        <div class="col">
            <input type="text" name="peso" class="form-control" id="peso" inputmode="decimal" autocomplete="off" @if (old('peso')!= null)
                value="{{old('peso')}}"
            @else
                value="0.00"
            @endif>
            @if ($errors->has('peso'))
               <span class="error text-danger" for="peso">{{ $errors->first('peso') }}</span>
            @endif  
        </div>
    </div>
    <label class="form-label" for="precio">Precio por kilo o unidad:</label>
    <div class="row">
        <div class="col">
            <input type="text" name="precio" id="precio" class="form-control" inputmode="decimal" autocomplete="off" value="{{old('precio')}}">
            @if ($errors->has('precio'))
               <span class="error text-danger" for="precio">{{ $errors->first('precio') }}</span>
            @endif  
        </div>
    </div>
    <label class="form-label" for="acuenta">Dinero a cuenta:</label>
    <input type="text" name="acuenta" id="acuenta" class="form-control" inputmode="decimal" autocomplete="off" value="0.00">
    @if ($errors->has('acuenta'))
        <span class="error text-danger" for="acuenta">{{ $errors->first('acuenta') }}</span>
    @endif
    <div class="row">
        <div class="col">
            <div id="total_box">Total: <strong id="total_vivo">—</strong> Bs | Resta: <strong id="resto_vivo">—</strong> Bs</div>
        </div>
    </div>
    <script src="{{asset('js/venta_total.js')}}"></script>
    <script>
        initVentaTotal({formId:"formulario",productoId:"producto",moldesId:"cantidad_moldes",pesoId:"peso",precioId:"precio",roundId:"tipo",acuentaId:"acuenta",totalId:"total_vivo",restoId:"resto_vivo",clienteId:"cliente",loteId:"lote"});
    </script>
    <div class="row" id="cont_btn">
        <div class="col"><a id="cancelar" href="{{route('menu')}}">Cancelar</a></div>
        <div class="col"><button id="enviar" type='submit'>Vender</button></div>
    </div>
</form>
<script>
    var contado=document.getElementById("contado")
    contado.onclick=function(){
        if(contado.value=="0"){contado.value="1"}else{contado.value="0"}
    }
    var tipo=document.getElementById("tipo")
    tipo.onclick=function(){
        if(tipo.value=="0"){tipo.value="1"}else{tipo.value="0"}
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
@endsection