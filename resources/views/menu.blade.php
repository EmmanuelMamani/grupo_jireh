@extends("header")
@section("opciones")
<a href="{{route('logout')}}" class="opciones_head">Salir</a>
<a href="{{route("perfil")}}" class="opciones_head">{{Auth::user()->Nombre}}</a>
@endsection
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("titulo", "Grupo JIREH")
@section("contenido")
<div class="max-w-3xl mx-auto px-3 py-4 space-y-5">
  @php
    $esAdmin = Auth::user()->Rol == 'Administrador';
  @endphp

  <section>
    <h4 class="text-sm font-semibold uppercase tracking-wide text-slate-500 mb-2">Vender</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <a href="{{route("venta")}}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3 active:scale-[0.99] transition">
        <span class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-2xl text-white" style="background-color:#125149"><x-icon name="shopping_cart"/></span>
        <span class="flex-1">
          <span class="block font-semibold text-slate-800">Pre-Venta</span>
          <span class="block text-xs text-slate-500">Venta con cliente y lote</span>
        </span>
      </a>
      <a href="{{route("venta_rapida")}}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3 active:scale-[0.99] transition">
        <span class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-2xl text-white" style="background-color:#125149"><x-icon name="shopping_cart_checkout"/></span>
        <span class="flex-1">
          <span class="block font-semibold text-slate-800">Venta rápida</span>
          <span class="block text-xs text-slate-500">Venta directa al contado</span>
        </span>
      </a>
      <a href="{{route("lista_reporte")}}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3 active:scale-[0.99] transition">
        <span class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-2xl text-white" style="background-color:#DA7922"><x-icon name="receipt_long"/></span>
        <span class="flex-1">
          <span class="block font-semibold text-slate-800">Pedidos</span>
          <span class="block text-xs text-slate-500">Lista por completar</span>
        </span>
        @if ($mis_pedidos > 0)
          <span class="shrink-0 text-xs font-bold px-2 py-1 rounded-full text-white" style="background-color:#DA7922">{{$mis_pedidos}}</span>
        @endif
      </a>
    </div>
  </section>

  <section>
    <h4 class="text-sm font-semibold uppercase tracking-wide text-slate-500 mb-2">Cobrar</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <a href="{{route("saldos")}}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3 active:scale-[0.99] transition">
        <span class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-2xl text-white" style="background-color:#125149"><x-icon name="payments"/></span>
        <span class="flex-1">
          <span class="block font-semibold text-slate-800">Cobranza</span>
          <span class="block text-xs text-slate-500">Cobrar deudas de clientes</span>
        </span>
      </a>
      <a href="{{route("registro_cliente")}}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3 active:scale-[0.99] transition">
        <span class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-2xl text-white" style="background-color:#125149"><x-icon name="group"/></span>
        <span class="flex-1">
          <span class="block font-semibold text-slate-800">Clientes</span>
          <span class="block text-xs text-slate-500">Registrar y consultar</span>
        </span>
      </a>
      <a href="{{route("transferir_lote")}}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3 active:scale-[0.99] transition">
        <span class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-2xl text-white" style="background-color:#125149"><x-icon name="swap_horiz"/></span>
        <span class="flex-1">
          <span class="block font-semibold text-slate-800">Transferir lote</span>
          <span class="block text-xs text-slate-500">Mover stock entre vendedores</span>
        </span>
      </a>
    </div>
  </section>

  @if ($esAdmin)
  <section>
    <h4 class="text-sm font-semibold uppercase tracking-wide text-slate-500 mb-2">Gestionar</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <a href="{{route("reporte_lotes")}}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3 active:scale-[0.99] transition">
        <span class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-2xl text-white" style="background-color:#DA7922"><x-icon name="local_shipping"/></span>
        <span class="flex-1">
          <span class="block font-semibold text-slate-800">Lotes</span>
          <span class="block text-xs text-slate-500">Compras y stock</span>
        </span>
      </a>
      <a href="{{route("registro_producto")}}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3 active:scale-[0.99] transition">
        <span class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-2xl text-white" style="background-color:#DA7922"><x-icon name="local_pizza"/></span>
        <span class="flex-1">
          <span class="block font-semibold text-slate-800">Productos</span>
          <span class="block text-xs text-slate-500">Catálogo y precios</span>
        </span>
      </a>
      <a href="{{route("registro_zona")}}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3 active:scale-[0.99] transition">
        <span class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-2xl text-white" style="background-color:#DA7922"><x-icon name="map"/></span>
        <span class="flex-1">
          <span class="block font-semibold text-slate-800">Zonas</span>
          <span class="block text-xs text-slate-500">Zonas de reparto</span>
        </span>
      </a>
      <a href="{{route("reporte_empleados")}}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3 active:scale-[0.99] transition">
        <span class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-2xl text-white" style="background-color:#DA7922"><x-icon name="business_center"/></span>
        <span class="flex-1">
          <span class="block font-semibold text-slate-800">Empleados</span>
          <span class="block text-xs text-slate-500">Personal y accesos</span>
        </span>
      </a>
    </div>
  </section>

  <section>
    <h4 class="text-sm font-semibold uppercase tracking-wide text-slate-500 mb-2">Finanzas</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <a href="{{route("registro_gasto")}}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3 active:scale-[0.99] transition">
        <span class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-2xl text-white" style="background-color:#125149"><x-icon name="monetization_on"/></span>
        <span class="flex-1">
          <span class="block font-semibold text-slate-800">Cuentas</span>
          <span class="block text-xs text-slate-500">Gastos e ingresos del día</span>
        </span>
      </a>
      <a href="{{route("estado_cuentas.index")}}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3 active:scale-[0.99] transition">
        <span class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-2xl text-white" style="background-color:#125149"><x-icon name="balance"/></span>
        <span class="flex-1">
          <span class="block font-semibold text-slate-800">Estado de cuentas</span>
          <span class="block text-xs text-slate-500">Reporte mensual</span>
        </span>
      </a>
      <a href="{{route("estadisticas_cuentas")}}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3 active:scale-[0.99] transition">
        <span class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-2xl text-white" style="background-color:#125149"><x-icon name="analytics"/></span>
        <span class="flex-1">
          <span class="block font-semibold text-slate-800">Estadísticas</span>
          <span class="block text-xs text-slate-500">Control de gastos</span>
        </span>
      </a>
    </div>
  </section>
  @endif
</div>
@endsection
