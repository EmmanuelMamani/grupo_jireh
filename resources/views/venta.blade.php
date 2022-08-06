@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario">
    <h3>Pre-Venta</h3>
    @csrf
    <label class="form-label">Zona:</label>
    <select name="zona" id="zona" class="form-select">
        <option value="0">Elije la zona</option>
        <option value="0">Norte</option>
    </select>
    <label class="form-label">Cliente:</label>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" value="0" id="tipo">
        <label class="form-check-label">
          Cliente empresa
        </label>
    </div>
    <select name="cliente" id="cliente" class="form-select">
        <option value="0">Elije un cliente</option>
        <option value="0">Martin Mamani</option>
    </select>
    <label class="form-label">Producto:</label>
    <select name="producto" id="producto" class="form-select">
        <option value="0">Elije un producto</option>
        <option value="0">Mozarella por kilo</option>
    </select>
    <label class="form-label">Lote:</label>
    <select name="lote" id="lote" class="form-select">
        <option value="0">Elije un lote</option>
        <option value="0">Martin Mamani - 100 - 27/07/2022</option>
    </select>
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
            <input type="text" name="cantidad_moldes" class="form-control">
        </div>
        <div class="col">
            <input type="text" name="peso" class="form-control" id="peso">
        </div>
    </div>
    <label class="form-label">Precio por kilo o unidad:</label>
    <div class="row">
        <div class="col-8">
            <input type="text" name="precio" id="precio" class="form-control">
        </div>
        <div class="col">
            <a href="#" id="calcular">Calcular</a>
        </div>
    </div>
    <h5 id="total">Costo total: 0 Bs</h5>
    <label class="form-label">Comprobante:</label>
    <input type="file" name="comprobante" id="comprobante" class="form-control" multiple>
    <div class="row" id="cont_btn">
        <div class="col"><button id="cancelar">Cancelar</button></div>
        <div class="col"><button id="enviar" disabled>Vender</button></div>
    </div>
    <input type="text" value="0" id="costo" name="costo" class ="oculto">
</form>
<script>
    //------------------Imagenes--------------------------
    var comprobante=document.getElementById("comprobante");
    comprobante.addEventListener('change', mostrar, 'false');
    var formulario=document.getElementById("formulario");
 
    function mostrar(e){
        var reader = new FileReader();
        var file = e.target.files;
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
    //--------------------------------------------
    var tipo=document.getElementById("tipo");
    var calculo=document.getElementById("calcular");
    var peso=document.getElementById("peso");
    var precio=document.getElementById("precio");
    var costo=document.getElementById("costo")
    var total=document.getElementById("total")
    var vender=document.getElementById("enviar")
    tipo.onclick=function(){
        if(tipo.value =="0"){
            tipo.value ="1"
        }else{tipo.value="0"}
    }
    calculo.onclick=function(){
        var aux1=0;
        var aux2=0;
        if(peso.value.match('^[0-9 .]+$')!=null && precio.value.match('^[0-9 .]+$')!=null ){
            if(peso.value.split('.').length > 1){
                if(peso.value.split('.')[1] != ""){
                    aux1=1;
                }
            }else{
                aux1=1;
            }
            if(precio.value.split('.').length > 1){
                if(precio.value.split('.')[1] != ""){
                    aux2=1;
                }
            }else{
                aux2=1;
            }
            if(aux1 == 1 && aux2==1){
            costo.value=(parseFloat(peso.value)*parseFloat(precio.value)).toFixed(parseInt(tipo.value))
            total.innerHTML="Costo total: "+costo.value+" Bs"
            vender.disabled=false
            }else{
                vender.disabled=true
            }
        }
    }
</script>
@endsection