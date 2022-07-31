@extends('header')
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("titulo","Grupo JIREH")
@section("contenido")
<form id="formulario">
<h3>Venta rapida</h3>
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
    <div class="form-check">
        <input class="form-check-input" type="checkbox" value="0" id="tipo">
        <label class="form-check-label">
          Con centavos
        </label>
    </div>
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
    <div class="row" id="cont_btn">
        <div class="col"><button id="cancelar">Cancelar</button></div>
        <div class="col"><button id="enviar" disabled>Vender</button></div>
    </div>
    <input type="text" value="0" id="costo" name="costo" class ="oculto">
</form>
<script>
    var tipo=document.getElementById("tipo");
    var calculo=document.getElementById("calcular");
    var peso=document.getElementById("peso");
    var precio=document.getElementById("precio");
    var costo=document.getElementById("costo")
    var total=document.getElementById("total")
    var vender=document.getElementById("vender")
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