@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form action="{{route('venta_completa',["id"=>$lista->id])}}" id="formulario" method="POST" enctype="multipart/form-data">
    <h3>Pre-Venta</h3>
    @csrf
    
    <label class="form-label">Cliente:</label>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" value="0" id="tipo" name="tipo">
        <label class="form-check-label">
          Cliente empresa
        </label>
    </div>
    <select name="cliente" id="cliente" class="form-select">
        <option value="{{$lista->cliente_id}}">{{$lista->cliente->Nombre}}</option>
        
    </select>
    <label class="form-label">Producto:</label>
    <select name="producto" id="producto" class="form-select">
        <option value="{{$lista->producto_id}}">{{$lista->producto->Nombre}} {{$lista->producto->Tipo}}</option>
    </select>
   
    <label class="form-label">Lote:</label>
    <select name="lote" id="lote" class="form-select">
        <option >Elije un lote</option>
        @foreach ($lotes as $lote)
            @if ($lote->ingreso->producto->Tipo==$lista->producto->Tipo && $lote->ingreso->producto->Nombre==$lista->producto->Nombre) 
                <option value='{{$lote->ingreso->id}}' @if(old('lote') == $lote->ingreso->id) selected @endif>{{$lote->ingreso->Proveedor}} - {{$lote->ingreso->CantMoldes}} - {{$lote->ingreso->created_at->format('Y-m-d')}}</option> 
            @endif
        @endforeach
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
        lote.innerHTML="<option>Elije un lote</option>";
        @foreach ($lotes as $lote)
                
                if("{{$lote->ingreso->producto->Tipo}}" == "{{$lista->producto->Tipo}}" && "{{$lote->ingreso->producto->Nombre}}"=="{{$lista->producto->Nombre}}"){
                    lote.innerHTML+="<option value='{{$lote->ingreso->id}}' @if(old('lote') == $lote->ingreso->id) selected @endif>{{$lote->ingreso->Proveedor}} - {{$lote->ingreso->CantMoldes}} - {{$lote->ingreso->created_at->format('Y-m-d')}}</option>";
                    if("{{old('lote')}}"=={{$lote->ingreso->id}}){
                        label.innerHTML="Cantidad restante en el lote: {{$lote->CantMoldes}}";
                    }
                }
        @endforeach
        
    </script>
       
    @endif
    <script>

        //------------------------------------------------------
        var lote_elegido=document.getElementById("lote");
        lote_elegido.addEventListener('change',(event)=>{
            var label=document.getElementById("cantidad_lotes");
            var id_lote=lote_elegido.options[lote_elegido.selectedIndex].value;
            console.log(lote_elegido.options[lote_elegido.selectedIndex].text);
            var texto=lote_elegido.options[lote_elegido.selectedIndex].text;
           
            @foreach ($lotes as $lote)
                if({{$lote->ingreso->id}}==id_lote){
                    label.innerHTML="Cantidad restante en el lote: {{$lote->CantMoldes}}";
                }
            @endforeach
            if(texto== "Elije un lote"){
                label.innerHTML="";
            }
        });


    </script>
    <div class="row">
        <div class="col">
            <label class="form-label">Cantidad de moldes:</label>
        </div>
        
        <div class="col">
            <label class="form-label">Peso total:</label>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <input type="text" name="cantidad_moldes" class="form-control" value="{{$lista->Unidades}}" readonly>
        </div>
        <div class="col">
            <input type="text" name="peso" class="form-control" id="peso" @if (old('peso')!= null)
                value="{{old('peso')}}"
            @else
                value="0.00"
            @endif>
            @if ($errors->has('peso'))
               <span class="error text-danger" for="peso">{{ $errors->first('peso') }}</span>
            @endif  
        </div>
    </div>
    <label class="form-label">Precio por kilo o unidad:</label>
    <div class="row">
        <div class="col">
            <input type="text" name="precio" id="precio" class="form-control" value="{{old('precio')}}">
            @if ($errors->has('precio'))
               <span class="error text-danger" for="precio">{{ $errors->first('precio') }}</span>
            @endif  
        </div>
    </div>
    <label class="form-label">Comprobante:</label>
    <input type="file" name="comprobante[]" id="comprobante" class="form-control" multiple="">
    @if ($errors->has('comprobante'))
               <span class="error text-danger" for="comprobante">{{ $errors->first('comprobante') }} </span>
            @endif  
    <div class="row" id="cont_btn">
        <div class="col"><a id="cancelar" href="/lista_reporte">Cancelar</a></div>
        <div class="col"><button id="enviar" type='submit'>Aceptar</button></div>
    </div>
    <input type="text" value="0" id="costo" name="costo" class ="oculto">
</form>
<script>
    //------------------Imagenes--------------------------
    var comprobante=document.getElementById("comprobante");
    comprobante.addEventListener('change', mostrar, 'false');
    comprobante.addEventListener('click', mostrar, 'false');
    var formulario=document.getElementById("formulario");
 
    function mostrar(e){
        var reader = new FileReader();
        var file = e.target.files;
        console.log(file);
        var contenedor=document.getElementById("imagenes");
        if(contenedor==null){
            contenedor=document.createElement("div");
            contenedor.id="imagenes";
        }else{
            contenedor.innerHTML="";
        }
    
        for (let i = 0; i < file.length; i++) {
            imagen=file[i];
            var reader = new FileReader();
            reader.readAsDataURL(imagen);
            reader.onload = function (e) {
                
                var img=document.createElement("img");
                img.className="imagen"+i;
                img.setAttribute('src', e.target.result);
                img.setAttribute('width', '40%');
                img.setAttribute('heigth', 'auto');
                contenedor.appendChild(img);

            }
        }
        comprobante.insertAdjacentElement("afterend", contenedor);
    }
</script>
@endsection