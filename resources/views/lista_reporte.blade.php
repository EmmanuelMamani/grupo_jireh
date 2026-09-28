@extends("header")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("lista_reporte")}}" class="opciones_head">Lista</a>
<a href="{{route("registro_lista")}}" class="opciones_head">Registro</a>
@endsection
@section("estilos")
@include('components.tablas_css')
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("titulo","Grupo JIREH")
@section("contenido")
<div class="max-w-6xl mx-auto px-3 py-4">
  <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
    <h3 class="text-xl font-bold text-slate-800">Mis pedidos</h3>
    <span id="transferir" class="cursor-pointer text-white text-sm font-medium px-4 py-2 rounded-xl" style="background-color:#DA7922">Transferir</span>
  </div>
  <div class="grid grid-cols-2 gap-3 mb-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Pedidos</p>
      <p class="text-lg font-bold text-slate-800">{{ $listas->count() }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
      <p class="text-xs text-slate-500">Unidades totales</p>
      <p class="text-lg font-bold text-slate-800">{{ number_format($listas->sum('Unidades'), 0, ',', '.') }}</p>
    </div>
  </div>
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table id="tabla" class="table w-full text-sm">
        <thead>
          <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
            <th class="px-3 py-2">Cliente</th>
            <th class="px-3 py-2">Producto</th>
            <th class="px-3 py-2 text-right">Unidades</th>
            <th class="px-3 py-2">Acciones</th>
            <th class="px-3 py-2 text-center">Transf.</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($listas as $lista )
            <tr class="fila border-b border-slate-100">
              <td class="px-3 py-2 font-semibold text-slate-800">{{$lista->cliente->Nombre}}</td>
              <td class="px-3 py-2 text-slate-600">{{$lista->producto->Nombre}}</td>
              <td class="px-3 py-2 text-right">{{ number_format($lista->Unidades, 0, ',', '.') }}</td>
              <td class="px-3 py-2">
                <div class="flex flex-wrap gap-1">
                  <form class="Eliminar inline" action="{{route("cancelar_lista",['id'=>$lista->id])}}" method="POST">@csrf<button class="text-xs px-2 py-1 rounded-lg bg-red-600 text-white">Cancelar</button></form>
                  <a href="{{route("completar_lista",['id'=>$lista->id])}}" class="text-xs px-2 py-1 rounded-lg text-white" style="background-color:#125149">Completar</a>
                </div>
              </td>
              <td class="px-3 py-2 text-center"><input class="transferir h-5 w-5 accent-[#125149]" type="checkbox" value="{{$lista->id}}"></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@include('components.tablas_js')
<script>
  $('#tabla').DataTable();
  $('.Eliminar').submit(function(e){
    e.preventDefault();
    var form = this;
    Swal.fire({
      title: '¿Estás seguro que quieres cancelar este pedido?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí',
      cancelButtonText: 'No'
    }).then((result) => { if (result.isConfirmed) { form.submit(); } });
  });
</script>
<script>
  $("#transferir").click(function(){
    var listas = [];
    $(".transferir:checked").each(function() {
      listas.push($(this).val());
    });
    listas=listas.join(',')
    Swal.fire({
      title: "<strong>Transferir pedidos</strong>",
      html: `
            <form action="{{route("transferir_lista")}}" method="post">
              @csrf
              <select class="p-2 w-11/12 mx-auto block rounded-xl border border-slate-300" name="user">
                @foreach ($usuarios as $user)
                    <option value="{{$user->id}}">{{$user->Nombre}}</option>
                @endforeach
              </select>
              <input type="hidden" name="lista" value="${listas}" >
                <button ${listas==''?'disabled':''} class="rounded-xl p-2 text-white mt-2" style="background-color:#125149" >Transferir</button>
            </form>
      `,
      showConfirmButton: false,
      showCloseButton: true
    });
  });
</script>
@endsection
