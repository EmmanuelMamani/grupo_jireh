@extends('header')
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
@endsection
@section("titulo","Grupo JIREH")
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <form action="{{route('venta_rapida')}}" id="formulario" method="POST" class="space-y-4">
      @csrf
      <h3 class="text-xl font-bold text-slate-800">Venta Rápida</h3>
      <section class="space-y-3">
        <h4 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Producto y lote</h4>
        <div>
          <x-ui-label for="producto">Producto</x-ui-label>
          <x-ui-select name="producto" id="producto">
            <option>Elige un producto</option>
            @foreach ($productos as $producto)
              <option value="{{$producto->id}}" @if(old('producto') == $producto->id ) selected @endif>{{$producto->Nombre}} {{$producto->Tipo}}</option>
            @endforeach
          </x-ui-select>
          <x-ui-error field="producto"/>
        </div>
        <div>
          <x-ui-label for="lote">Lote</x-ui-label>
          <x-ui-select name="lote" id="lote">
            <option>Elige un lote</option>
          </x-ui-select>
          <x-ui-error field="lote"/>
          <p id="cantidad_lotes" class="text-xs text-slate-500 mt-1"></p>
        </div>
      </section>
      <section class="space-y-3">
        <h4 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Montos</h4>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="0" id="centavos" name="centavos">
          <label class="form-check-label" for="centavos">Redondear total a entero</label>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <x-ui-label for="cantidad_moldes">Cantidad de moldes</x-ui-label>
            <x-ui-input type="text" name="cantidad_moldes" id="cantidad_moldes" inputmode="numeric" autocomplete="off" value="{{old('cantidad_moldes')}}"/>
            <x-ui-error field="cantidad_moldes"/>
          </div>
          <div>
            <x-ui-label for="peso">Peso total</x-ui-label>
            <x-ui-input type="text" name="peso" id="peso" inputmode="decimal" autocomplete="off" @if (old('peso')!= null) value="{{old('peso')}}" @else value="0.00" @endif/>
            <x-ui-error field="peso"/>
          </div>
        </div>
        <div>
          <x-ui-label for="precio">Precio por kilo o unidad</x-ui-label>
          <x-ui-input type="text" name="precio" id="precio" inputmode="decimal" autocomplete="off" value="{{old('precio')}}"/>
          <x-ui-error field="precio"/>
        </div>
        <div id="total_box" class="rounded-xl text-white text-center px-3 py-3" style="background-color:#125149">Total: <strong id="total_vivo" class="text-xl">—</strong> Bs</div>
      </section>
      <div class="grid grid-cols-2 gap-3 pt-1" id="cont_btn">
        <x-ui-ghost href="{{route('menu')}}" id="cancelar">Cancelar</x-ui-ghost>
        <x-ui-primary id="enviar" type="submit">Vender</x-ui-primary>
      </div>
    </form>
  </x-ui-card>
</div>
<script src="{{asset('js/venta_total.js')}}"></script>
<script>
    initVentaTotal({formId:"formulario",productoId:"producto",moldesId:"cantidad_moldes",pesoId:"peso",precioId:"precio",roundId:"centavos",totalId:"total_vivo",loteId:"lote"});
</script>
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
    var producto=document.getElementById("producto");
    producto.addEventListener('change',(event)=>{
        var producto_text=producto.options[producto.selectedIndex].text;
        if(producto_text=="Elige un producto"){
            var label=document.getElementById("cantidad_lotes");
            label.innerHTML="";
        }
        producto_text=producto_text.split(" ");
        var unidad=producto_text[producto_text.length-1];
        var label=document.getElementById("cantidad_lotes");
        var peso=document.getElementById("peso");
        if(unidad=="unidad"){
            peso.disabled=true;
        }else{
            peso.disabled=false;
        }
        var producto_id=producto.options[producto.selectedIndex].value;
        var lote=document.getElementById("lote");
        lote.innerHTML="<option>Elige un lote</option>";
        @foreach ($lotes as $lote)
            if(producto_id=={{$lote->ingreso->producto_id}} &&  {{$lote->ingreso->Activo}} == 1){
                lote.innerHTML+="<option value='{{$lote->ingreso->id}}'>{{$lote->ingreso->Proveedor}} - {{$lote->ingreso->CantMoldes}} - {{$lote->ingreso->created_at->format('Y-m-d')}}</option>";
            }
        @endforeach
    });
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
<script>
    var centavos=document.getElementById("centavos")
    centavos.onclick=function(){
        if(centavos.value=="0"){centavos.value="1"}else{centavos.value="0"}
    }
</script>
@endsection
