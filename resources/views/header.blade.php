<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8"/>
    <link rel="icon" href="{{asset('img/logo1.ico')}}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta
      name="description"
      content="Grupo Jireh - Sistema interno de ventas, clientes y cuentas."
    />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('css/login.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500&display=swap" rel="stylesheet">
    @yield('estilos')
    @stack('head-scripts')
    <title>@yield("titulo", "Grupo Jireh")</title>
  </head>
  <body>
    <div id="contenedor_carga">
      <div class="loader"></div>
  </div>
    @php
    $secciones = [
      'login' => 'Inicia sesión', 'menu' => 'Inicio',
      'venta' => 'Pre-Venta', 'venta_rapida' => 'Venta rápida', 'venta_completa' => 'Completar venta',
      'ventas_pendientes' => 'Ventas pendientes', 'venta_detalle' => 'Detalle de venta',
      'venta_devolucion' => 'Devolución', 'devolucion' => 'Devolución', 'editar_venta' => 'Editar venta',
      'saldos' => 'Cobranza', 'saldo_pasado' => 'Deudas pasadas',
      'registro_cliente' => 'Registro de cliente', 'reporte_cliente' => 'Reporte de clientes',
      'editar_cliente' => 'Editar cliente', 'ver_tienda' => 'Foto de tienda',
      'ventas_periodo' => 'Kardex por período', 'reporte_periodo_ventas' => 'Kardex del cliente',
      'reporte_ventas' => 'Lotes para vender', 'reporte_lote_ventas' => 'Ventas del lote',
      'reporte_lotes' => 'Reporte de lotes', 'reporte_lotes_total' => 'Reporte total de lotes',
      'registro_lote' => 'Registro de lote', 'editar_lote' => 'Editar lote',
      'registro_producto' => 'Registro de producto', 'reporte_producto' => 'Reporte de productos',
      'registro_zona' => 'Registro de zona', 'reporte_zona' => 'Reporte de zonas',
      'registro_empleado' => 'Registro de empleado', 'reporte_empleados' => 'Reporte de empleados',
      'registro_gasto' => 'Registro de gasto', 'reporte_cuenta' => 'Reporte de cuentas',
      'reporte_historico' => 'Histórico de cuentas', 'reporte_diario' => 'Reporte diario',
      'detalle_cuenta' => 'Detalle de cuenta', 'cuentas_periodo' => 'Cuentas por período',
      'reporte_periodo' => 'Reporte de cuentas', 'estadisticas_cuentas' => 'Estadísticas',
      'estado_cuentas.index' => 'Estado de cuentas', 'lista_reporte' => 'Mis pedidos',
      'registro_lista' => 'Registrar pedido', 'completar_lista' => 'Completar pedido',
      'transferir_lote' => 'Transferir lote', 'perfil' => 'Mi perfil',
    ];
    $seccionActual = $secciones[Route::currentRouteName() ?? ''] ?? 'Grupo JIREH';
    @endphp
    <header> <div id="sup"></div>
      <nav class="navbar">
        <div class="container-fluid" id="navbar">
          <a class="navbar-brand" href="{{route('menu')}}" id="cont_nav">
            <img src="{{asset('img/logo.png')}}" alt="Grupo Jireh" width="36" class="d-inline-block align-text-top">
            <span id="titulo"><small>Grupo JIREH</small><strong id="seccion_actual">{{ $seccionActual }}</strong></span>
          </a>
          @yield("opciones")
          @auth
          <details class="user-menu">
            <summary class="user-chip" aria-label="Cuenta de {{ Auth::user()->Nombre }}">
              <span class="user-avatar">{{ strtoupper(mb_substr(Auth::user()->Nombre, 0, 1)) }}</span>
            </summary>
            <div class="user-drop">
              <p class="user-drop-name">{{ Auth::user()->Nombre }}</p>
              <p class="user-drop-rol">{{ Auth::user()->Rol }}</p>
              <a href="{{route('perfil')}}">Mi perfil</a>
              <a href="{{route('logout')}}">Salir</a>
            </div>
          </details>
          @endauth
          <button type="button" id="menu" aria-label="Mostrar u ocultar navegación" aria-expanded="false" aria-controls="navbar"><span id="menu_open"><x-icon name="menu"/></span><span id="menu_close" hidden><x-icon name="close"/></span></button>
            
        </div>
      </nav><div id="inf"></div>
    </header>
    @yield("contenido")
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('registrar')=='ok' || session('eliminar')=='ok' || session('editar')=='ok')
    <script>
      Swal.fire({
      position: 'center',
      icon: 'success',
      title: "{{ session('registrar')=='ok' ? 'Registro exitoso' : (session('eliminar')=='ok' ? 'Registro eliminado' : 'Edición exitosa') }}",
      showConfirmButton: false,
      timer: 1500
  })
</script>
@endif
  <script>
    (function(){
      var menu=document.getElementById("menu");
      var navbar=document.getElementById("navbar");
      var opt=document.getElementsByClassName("opciones_head");
      var headerEl=document.querySelector("header");
      window.addEventListener("scroll",function(){
        if(headerEl)headerEl.classList.toggle("scrolled",window.scrollY>8);
      },{passive:true});
      var current=(location.pathname.replace(/\/$/,'')||'/');
      for(var j=0;j<opt.length;j++){
        var href=opt[j].getAttribute('href');
        if(!href)continue;
        var tmp=document.createElement('a');tmp.href=href;
        if((tmp.pathname.replace(/\/$/,'')||'/')===current){opt[j].classList.add('activa');}
      }
      if(opt.length<=1){
        menu.style.display="none";
        navbar.classList.add("open");
        return;
      }
      menu.addEventListener("click",function(){
        var open=navbar.classList.toggle("open");
        menu.setAttribute("aria-expanded",open?"true":"false");
        document.getElementById("menu_open").hidden=open;
        document.getElementById("menu_close").hidden=!open;
      });
      // Anti doble-envío global: en todo POST muestra el loader y bloquea el botón.
      document.addEventListener("submit",function(e){
        var form=e.target;
        if(!form||form.tagName!=="FORM"||form.method.toLowerCase()!=="post")return;
        var carga=document.getElementById("contenedor_carga");
        if(carga)carga.style.visibility="visible";
        var btn=form.querySelector('button[type="submit"], button:not([type])');
        if(btn)btn.disabled=true;
      });
    })();
  </script>
  @stack('scripts')
  </body>
</html>