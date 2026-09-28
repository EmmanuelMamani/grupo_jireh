@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <form action="{{route('venta_completa',["id"=>$lista->id])}}" id="formulario" method="POST" class="space-y-4">
      <h3 class="text-xl font-bold text-slate-800">Pre-Venta</h3>
      @csrf
      <section class="space-y-3">
        <h4 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Cliente</h4>
        <div class="flex flex-wrap gap-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="0" id="tipo" name="tipo">
            <label class="form-check-label" for="tipo">Redondear total a entero</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="contado" name="contado" value="0">
            <label class="form-check-label" for="contado">Al contado</label>
          </div>
        </div>
        <div>
          <x-ui-label for="cliente">Cliente</x-ui-label>
          <x-ui-select name="cliente" id="cliente">
            <option value="{{$lista->cliente_id}}">{{$lista->cliente->Nombre}}</option>
          </x-ui-select>
        </div>
      </section>
      <section class="space-y-3">
        <h4 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Producto y lote</h4>
        <div>
          <x-ui-label for="producto">Producto</x-ui-label>
          <x-ui-select name="producto" id="producto">
            <option value="{{$lista->producto_id}}">{{$lista->producto->Nombre}} {{$lista->producto->Tipo}}</option>
          </x-ui-select>
        </div>
        <div>
          <x-ui-label for="lote">Lote</x-ui-label>
          <x-ui-select name="lote" id="lote">
            <option>Elige un lote</option>
            @foreach ($lotes as $lote)
              @if ($lote->ingreso->producto->Tipo==$lista->producto->Tipo && $lote->ingreso->producto->Nombre==$lista->producto->Nombre)
                <option value='{{$lote->ingreso->id}}' @if(old('lote') == $lote->ingreso->id) selected @endif>{{$lote->ingreso->Proveedor}} - {{$lote->ingreso->CantMoldes}} - {{$lote->ingreso->created_at->format('Y-m-d')}}</option>
              @endif
            @endforeach
          </x-ui-select>
          <x-ui-error field="lote"/>
          <p id="cantidad_lotes" class="text-xs text-slate-500 mt-1"></p>
        </div>
      </section>
      <section class="space-y-3">
        <h4 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Montos</h4>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <x-ui-label for="cantidad_moldes">Cantidad de moldes</x-ui-label>
            <x-ui-input type="text" name="cantidad_moldes" id="cantidad_moldes" inputmode="numeric" autocomplete="off" value="{{$lista->Unidades}}"/>
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
        <x-ui-ghost href="{{route('lista_reporte')}}" id="cancelar">Cancelar</x-ui-ghost>
        <x-ui-primary id="enviar" type="submit">Aceptar</x-ui-primary>
      </div>
      <input type="text" value="0" id="costo" name="costo" class="oculto" style="display:none">
    </form>
  </x-ui-card>
</div>
<script src="{{asset('js/venta_total.js')}}"></script>
<script>
    initVentaTotal({formId:"formulario",productoId:"producto",moldesId:"cantidad_moldes",pesoId:"peso",precioId:"precio",roundId:"tipo",totalId:"total_vivo",clienteId:"cliente",loteId:"lote"});
</script>
@if(old('lote')!=null)
<script>
    var producto_id=producto.options[producto.selectedIndex].value;
    var label=document.getElementById("cantidad_lotes");
    var lote=document.getElementById("lote");
    lote.innerHTML="<option>Elige un lote</option>";
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
    var contado=document.getElementById("contado")
    contado.onclick=function(){
        if(contado.value=="0"){contado.value="1"}else{contado.value="0"}
    }
    var tipo=document.getElementById("tipo")
    tipo.onclick=function(){
        if(tipo.value=="0"){tipo.value="1"}else{tipo.value="0"}
    }
</script>
@endsection
