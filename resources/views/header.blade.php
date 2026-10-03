<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8"/>
    <link rel="icon" href="{{asset('img/logo1.ico')}}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta
      name="description"
      content="Grupo Jireh - Sistema interno de ventas, clientes y cuentas."
    />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('css/login.css')}}">
    <link rel="stylesheet" href="{{asset('css/header.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @yield('estilos')
    @stack('head-scripts')
    <title>@yield("titulo", "Grupo Jireh")</title>
  </head>
  <body>
    <a class="jh-skip" href="#contenido">Saltar al contenido</a>
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
    $rutaActual = Route::currentRouteName() ?? '';
    $seccionActual = $secciones[$rutaActual] ?? 'Grupo JIREH';
    $esInicio = in_array($rutaActual, ['menu', 'login', '']);
    $fechaCorta = date('d/m/Y');
    // Icono contextual por familia de ruta (se usa en la contextbar)
    $iconoSeccion = 'list';
    if (str_contains($rutaActual, 'venta') || str_contains($rutaActual, 'lote_ventas')) $iconoSeccion = 'shopping_cart';
    elseif ($rutaActual === 'venta_rapida') $iconoSeccion = 'shopping_cart_checkout';
    elseif (str_contains($rutaActual, 'saldo')) $iconoSeccion = 'payments';
    elseif (str_contains($rutaActual, 'cliente') || str_contains($rutaActual, 'tienda')) $iconoSeccion = 'group';
    elseif (str_contains($rutaActual, 'lote') || str_contains($rutaActual, 'transferir')) $iconoSeccion = 'local_shipping';
    elseif (str_contains($rutaActual, 'producto')) $iconoSeccion = 'local_pizza';
    elseif (str_contains($rutaActual, 'zona')) $iconoSeccion = 'map';
    elseif (str_contains($rutaActual, 'empleado')) $iconoSeccion = 'business_center';
    elseif (str_contains($rutaActual, 'gasto') || str_contains($rutaActual, 'cuenta') || str_contains($rutaActual, 'reporte_periodo') || str_contains($rutaActual, 'detalle_cuenta')) $iconoSeccion = 'monetization_on';
    elseif (str_contains($rutaActual, 'estadistica') || str_contains($rutaActual, 'estado_cuentas')) $iconoSeccion = 'analytics';
    elseif (str_contains($rutaActual, 'lista') || str_contains($rutaActual, 'pedido')) $iconoSeccion = 'receipt_long';
    elseif ($rutaActual === 'menu') $iconoSeccion = 'home';
    elseif ($rutaActual === 'perfil') $iconoSeccion = 'person';
    @endphp
    <header class="jh-header" id="jh_header">
      <div class="jh-accent" aria-hidden="true"></div>

      {{-- ══ Fila 1 · Topbar ══ --}}
      <div class="jh-topbar">
        <div class="jh-topbar-inner">
          <a class="jh-brand" href="{{route('menu')}}" aria-label="Grupo Jireh · Inicio">
            <span class="jh-brand-badge"><img src="{{asset('img/logo.png')}}" alt="" width="26" height="26"></span>
            <span class="jh-brand-text">
              <small>Grupo JIREH</small>
              <strong id="jh_section">{{ $seccionActual }}</strong>
            </span>
          </a>

          <nav class="jh-nav" aria-label="Navegación principal">
            <div class="jh-nav-pill" id="jh_nav_pill">@yield("opciones")</div>
          </nav>

          <div class="jh-actions">
            @auth
            <div class="jh-user" id="jh_user">
              <button type="button" class="jh-user-chip" id="jh_user_btn" aria-haspopup="menu" aria-expanded="false" aria-controls="jh_user_menu" aria-label="Cuenta de {{ Auth::user()->Nombre }}">
                <span class="jh-avatar" aria-hidden="true">{{ strtoupper(mb_substr(Auth::user()->Nombre, 0, 1)) }}</span>
                <span class="jh-user-meta">
                  <span class="jh-user-name">{{ Auth::user()->Nombre }}</span>
                  <span class="jh-user-rol">{{ Auth::user()->Rol }}</span>
                </span>
                <span class="jh-chevron" aria-hidden="true"><x-icon name="expand_more"/></span>
              </button>
              <div class="jh-user-menu" id="jh_user_menu" role="menu" aria-labelledby="jh_user_btn" hidden>
                <p class="jh-user-menu-head">
                  <span class="jh-user-menu-name">{{ Auth::user()->Nombre }}</span>
                  <span class="jh-user-menu-rol">{{ Auth::user()->Rol }}</span>
                </p>
                <a href="{{route('perfil')}}" role="menuitem"><x-icon name="person"/> Mi perfil</a>
                <a href="{{route('logout')}}" role="menuitem" class="is-danger"><x-icon name="logout"/> Salir</a>
              </div>
            </div>
            <button type="button" class="jh-burger" id="jh_burger" aria-label="Abrir menú" aria-expanded="false" aria-controls="jh_drawer" hidden>
              <span id="jh_burger_open"><x-icon name="menu"/></span>
              <span id="jh_burger_close" hidden><x-icon name="close"/></span>
            </button>
            @endauth
          </div>
        </div>

        {{-- Chips scrollables (móvil): se rellenan por JS clonando @yield("opciones") --}}
        <div class="jh-chips" id="jh_chips" hidden>
          <div class="jh-chips-track" id="jh_chips_track" role="navigation" aria-label="Secciones"></div>
        </div>
      </div>

      {{-- ══ Fila 2 · Contextbar: breadcrumb + fecha ══ --}}
      @auth
      <div class="jh-contextbar">
        <nav class="jh-crumb" aria-label="Ubicación actual">
          <a href="{{route('menu')}}"><x-icon name="home"/><span>Inicio</span></a>
          @unless($esInicio)
          <span class="jh-crumb-sep" aria-hidden="true">/</span>
          <span class="jh-crumb-current" aria-current="page"><x-icon name="{{$iconoSeccion}}"/><span>{{ $seccionActual }}</span></span>
          @endunless
        </nav>
        <span class="jh-date"><x-icon name="calendar_month"/><span>{{ $fechaCorta }}</span></span>
      </div>
      @endauth
    </header>

    {{-- ══ Drawer móvil ══ --}}
    @auth
    <div class="jh-backdrop" id="jh_backdrop" hidden aria-hidden="true"></div>
    <aside class="jh-drawer" id="jh_drawer" role="dialog" aria-modal="true" aria-label="Menú de navegación" aria-hidden="true">
      <div class="jh-drawer-head">
        <span class="jh-avatar jh-avatar-lg" aria-hidden="true">{{ strtoupper(mb_substr(Auth::user()->Nombre, 0, 1)) }}</span>
        <p class="jh-drawer-name">{{ Auth::user()->Nombre }}</p>
        <p class="jh-drawer-rol">{{ Auth::user()->Rol }}</p>
        <div class="jh-drawer-head-actions">
          <a href="{{route('perfil')}}"><x-icon name="person"/> Mi perfil</a>
          <a href="{{route('logout')}}" class="is-danger"><x-icon name="logout"/> Salir</a>
        </div>
        <button type="button" class="jh-drawer-close" id="jh_drawer_close" aria-label="Cerrar menú"><x-icon name="close"/></button>
      </div>
      <p class="jh-drawer-label">Secciones</p>
      <nav class="jh-drawer-nav" id="jh_drawer_nav" aria-label="Secciones"></nav>
      <div class="jh-drawer-foot">
        <a href="{{route('menu')}}"><x-icon name="home"/> Volver al inicio</a>
      </div>
    </aside>
    @endauth

    <main id="contenido">
    @yield("contenido")
    </main>
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
      "use strict";
      var header = document.getElementById("jh_header");
      var pill = document.getElementById("jh_nav_pill");
      var burger = document.getElementById("jh_burger");
      var burgerOpen = document.getElementById("jh_burger_open");
      var burgerClose = document.getElementById("jh_burger_close");
      var drawer = document.getElementById("jh_drawer");
      var backdrop = document.getElementById("jh_backdrop");
      var drawerNav = document.getElementById("jh_drawer_nav");
      var drawerClose = document.getElementById("jh_drawer_close");
      var chips = document.getElementById("jh_chips");
      var chipsTrack = document.getElementById("jh_chips_track");
      var userBtn = document.getElementById("jh_user_btn");
      var userMenu = document.getElementById("jh_user_menu");

      /* ── Sombra al hacer scroll ── */
      window.addEventListener("scroll", function(){
        if (header) header.classList.toggle("scrolled", window.scrollY > 8);
      }, { passive: true });

      /* ── Recolectar links de @yield("opciones") ── */
      var links = pill ? Array.prototype.slice.call(pill.querySelectorAll("a.opciones_head")) : [];
      var current = (location.pathname.replace(/\/$/, "") || "/");

      function iconFor(a){
        var forced = a.getAttribute("data-icon");
        if (forced) return forced;
        var t = (a.textContent || "").trim().toLowerCase();
        var href = (a.getAttribute("href") || "").toLowerCase();
        if (a.id === "flecha" || t === "" || t === "←" || t === "volver") return "arrow_back";
        if (t.indexOf("inicio") === 0) return "home";
        if (t.indexOf("pendiente") >= 0) return "history";
        if (t.indexOf("rápida") >= 0 || t.indexOf("rapida") >= 0) return "shopping_cart_checkout";
        if (t.indexOf("venta") >= 0 && t.length < 8) return "shopping_cart";
        if (t.indexOf("completar") >= 0) return "edit";
        if (t.indexOf("detalle") >= 0) return "list";
        if (t.indexOf("devoluci") >= 0) return "history";
        if (t.indexOf("kardex") >= 0 || t.indexOf("período") >= 0 || t.indexOf("periodo") >= 0) return "calendar_month";
        if (t.indexOf("cobranza") >= 0 || t.indexOf("deuda") >= 0 || t.indexOf("saldo") >= 0) return "payments";
        if (t.indexOf("cliente") >= 0 || t.indexOf("tienda") >= 0) return "group";
        if (t.indexOf("transferir") >= 0 || t.indexOf("lote") >= 0) return "local_shipping";
        if (t.indexOf("producto") >= 0) return "local_pizza";
        if (t.indexOf("zona") >= 0) return "map";
        if (t.indexOf("empleado") >= 0) return "business_center";
        if (t.indexOf("gasto") >= 0 || t.indexOf("cuenta") >= 0 || t.indexOf("r.") >= 0 || t.indexOf("hist") >= 0) return "monetization_on";
        if (t.indexOf("estad") >= 0) return "analytics";
        if (t.indexOf("pedido") >= 0 || t.indexOf("lista") >= 0) return "receipt_long";
        if (t.indexOf("registro") >= 0) return "add";
        if (t.indexOf("reporte") >= 0) return "list";
        if (href.indexOf("perfil") >= 0) return "person";
        return "list";
      }

      function isActive(a){
        var href = a.getAttribute("href");
        if (!href) return false;
        var tmp = document.createElement("a");
        tmp.href = href;
        return (tmp.pathname.replace(/\/$/, "") || "/") === current;
      }

      var ICON_SVG = {
        home: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="M160-120v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z"/></svg>',
        shopping_cart: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="M236-102q-21-21-21-51t21-51q21-21 51-21t51 21q21 21 21 51t-21 51q-21 21-51 21t-51-21Zm400 0q-21-21-21-51t21-51q21-21 51-21t51 21q21 21 21 51t-21 51q-21 21-51 21t-51-21ZM235-741l110 228h288l125-228H235Zm-30-60h589q23 0 35 21t0 42L694-495q-11 19-29 30.5T627-453H324l-56 104h491v60H277q-42 0-60.5-28t.5-63l64-118-152-322H51v-60h117l37 79Zm140 288h288-288Z"/></svg>',
        shopping_cart_checkout: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="m480-574-42-42 74-74H330v-60h182l-74-74 42-42 146 146-146 146ZM239-101q-21-21-21-51t21-51q21-21 51-21t51 21q21 21 21 51t-21 51q-21 21-51 21t-51-21Zm404 0q-21-21-21-51t21-51q21-21 51-21t51 21q21 21 21 51t-21 51q-21 21-51 21t-51-21ZM62-820v-60h116l170 364h288l160-280h67L701-493q-11 19-29 30.5T634-451H331l-56 104h491v60H284q-38 0-57-30t-3-61l64-118-148-324H62Z"/></svg>',
        payments: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="M540-420q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM220-280q-25 0-42-18t-18-42v-400q0-25 18-42t42-18h640q25 0 42 18t18 42v400q0 25-18 42t-42 18H220Zm100-60h440q0-42 29-71t71-29v-200q-42 0-71-29t-29-71H320q0 42-29 71t-71 29v200q42 0 71 29t29 71Z"/></svg>',
        group: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="M38-160v-94q0-35 18-63.5t50-42.5q73-32 131.5-46T358-420q62 0 120 14t131 46q32 14 50.5 42.5T678-254v94H38Zm700 0v-94q0-63-32-103.5T622-423q69 8 130 23.5t99 35.5q33 19 52 47t19 63v94H738ZM250-523q-42-42-42-108t42-108q42-42 108-42t108 42q42 42 42 108t-42 108q-42 42-108 42t-108-42Z"/></svg>',
        local_shipping: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="M141-195q-34-34-34-84H40v-461q0-24 18-42t42-18h579v167h105l136 181v173h-71q0 49-34 84t-84 34q-49 0-84-34t-34-84H342q0 49-34 84t-84 34Z"/></svg>',
        local_pizza: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="M480-80 80-685q86-72 188-113.5T480-840q110 0 212 41.5T880-685L480-80Z"/></svg>',
        map: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="m612-120-263-93-179 71q-17 9-33.5-1T120-173v-558q0-13 7.5-23t19.5-15l202-71 263 92 178-71q17-8 33.5 1.5T840-788v565q0 11-7.5 19T814-192l-202 72Z"/></svg>',
        business_center: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="M140-120q-24 0-42-18t-18-42v-480q0-24 18-42t42-18h180v-100q0-24 18-42t42-18h200q24 0 42 18t18 42v100h180q24 0 42 18t18 42v480q0 24-18 42t-42 18H140Z"/></svg>',
        monetization_on: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="M451-193h55v-52q61-7 95-37.5t34-81.5q0-51-29-83t-98-61q-58-24-84-43t-26-51q0-31 22.5-49t61.5-18q30 0 52 14t37 42l48-23q-17-35-45-55t-66-24v-51h-55v51q-51 7-80.5 37.5T343-602q0 49 30 78t90 54q67 28 92 50.5t25 55.5q0 32-26.5 51.5T487-293q-39 0-69.5-22T375-375l-51 17q21 46 51.5 72.5T451-247v54Z"/></svg>',
        analytics: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="M284-277h60v-205h-60v205Zm332 0h60v-420h-60v420Zm-166 0h60v-118h-60v118Zm0-205h60v-60h-60v60ZM180-120q-24 0-42-18t-18-42v-600q0-24 18-42t42-18h600q24 0 42 18t18 42v600q0 24-18 42t-42 18H180Z"/></svg>',
        receipt_long: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="M222-80q-44 0-74-31t-31-75v-125h127v-570l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60v695q0 44-31 74t-74 30H222Zm516-60q20 0 32.5-12.5T783-185v-595H304v470h389v125q0 20 12.5 32.5T738-140Z"/></svg>',
        calendar_month: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="M180-80q-24 0-42-18t-18-42v-620q0-24 18-42t42-18h65v-60h65v60h340v-60h65v60h65q24 0 42 18t18 42v620q0 24-18 42t-42 18H180Zm0-60h600v-430H180v430Z"/></svg>',
        add: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>',
        list: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="M120-280v-80h360v80H120Zm0-160v-80h480v80H120Zm0-160v-80h480v80H120Zm520 440v-80h240v80H640Zm0-160v-80h240v80H640Zm0-160v-80h240v80H640Z"/></svg>',
        history: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="m627-287 45-45-159-160v-201h-60v225l174 181ZM480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-82 31.5-155t86-127.5Q252-817 325-848.5T480-880q82 0 155 31.5t127.5 86Q817-708 848.5-635T880-480q0 82-31.5 155t-86 127.5Q708-143 635-111.5T480-80Z"/></svg>',
        edit: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Z"/></svg>',
        person: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z"/></svg>',
        arrow_back: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor" aria-hidden="true" class="jireh-icon"><path d="m274-450 248 248-42 42-320-320 320-320 42 42-248 248h526v60H274Z"/></svg>'
      };

      /* ── Enriquecer pills desktop: icono + estado activo ── */
      links.forEach(function(a){
        var active = isActive(a);
        if (active) {
          a.classList.add("activa");
          a.setAttribute("aria-current", "page");
        }
        if (!a.querySelector("svg")) {
          var ic = document.createElement("span");
          ic.className = "jh-link-ic";
          ic.setAttribute("aria-hidden", "true");
          ic.innerHTML = ICON_SVG[iconFor(a)] || ICON_SVG.list;
          a.prepend(ic);
        } else {
          a.classList.add("has-svg");
        }
      });

      /* ── Chips móviles + drawer: clonar desde la misma fuente ── */
      function buildClone(target, cls){
        links.forEach(function(a){
          var c = document.createElement("a");
          c.href = a.href;
          c.className = cls + (a.classList.contains("activa") ? " activa" : "");
          if (a.classList.contains("activa")) c.setAttribute("aria-current", "page");
          if (a.id === "flecha") c.setAttribute("aria-label", a.getAttribute("aria-label") || "Volver");
          var label = (a.textContent || "").trim() || "Volver";
          c.innerHTML = '<span class="jh-link-ic" aria-hidden="true">' +
            (ICON_SVG[iconFor(a)] || ICON_SVG.list) + "</span><span></span>";
          c.lastChild.textContent = label;
          target.appendChild(c);
        });
      }

      if (links.length > 1) {
        if (chips && chipsTrack) {
          buildClone(chipsTrack, "jh-chip opciones_head_clone");
          chips.hidden = false;
          // Centrar el chip activo
          var act = chipsTrack.querySelector(".activa");
          if (act && act.scrollIntoView) {
            act.scrollIntoView({ block: "nearest", inline: "center" });
          }
        }
        if (drawerNav) buildClone(drawerNav, "jh-drawer-link");
        if (burger) burger.hidden = false;
        if (header) header.classList.add("has-nav");
      } else {
        if (header) header.classList.add("has-single");
        if (chips) chips.hidden = true;
        // Con 0-1 links no hace falta drawer
        if (burger) burger.hidden = true;
      }

      /* ── Drawer ── */
      var lastFocus = null;
      function setDrawer(open){
        if (!drawer || !backdrop || !burger) return;
        lastFocus = open ? document.activeElement : lastFocus;
        drawer.classList.toggle("open", open);
        drawer.setAttribute("aria-hidden", open ? "false" : "true");
        backdrop.hidden = !open;
        document.body.classList.toggle("jh-locked", open);
        burger.setAttribute("aria-expanded", open ? "true" : "false");
        burger.setAttribute("aria-label", open ? "Cerrar menú" : "Abrir menú");
        if (burgerOpen) burgerOpen.hidden = open;
        if (burgerClose) burgerClose.hidden = !open;
        if (open) {
          var f = drawer.querySelector(".jh-drawer-close, a");
          if (f) f.focus({ preventScroll: true });
        } else if (lastFocus && lastFocus.focus) {
          lastFocus.focus({ preventScroll: true });
        }
      }
      if (burger) burger.addEventListener("click", function(){
        setDrawer(!drawer.classList.contains("open"));
      });
      if (drawerClose) drawerClose.addEventListener("click", function(){ setDrawer(false); });
      if (backdrop) backdrop.addEventListener("click", function(){ setDrawer(false); });
      document.addEventListener("keydown", function(e){
        if (e.key === "Escape") { setDrawer(false); closeUser(); }
      });
      if (drawerNav) drawerNav.addEventListener("click", function(e){
        if (e.target.closest("a")) setDrawer(false);
      });
      // Swipe para cerrar (drawer lateral izquierdo)
      var touchX = null;
      if (drawer) {
        drawer.addEventListener("touchstart", function(e){
          touchX = e.touches[0].clientX;
        }, { passive: true });
        drawer.addEventListener("touchmove", function(e){
          if (touchX === null) return;
          if (e.touches[0].clientX - touchX < -60) { setDrawer(false); touchX = null; }
        }, { passive: true });
      }

      /* ── Menú de usuario accesible ── */
      function closeUser(){
        if (!userBtn || !userMenu) return;
        userBtn.setAttribute("aria-expanded", "false");
        userMenu.hidden = true;
      }
      if (userBtn && userMenu) {
        userBtn.addEventListener("click", function(e){
          e.stopPropagation();
          var open = userMenu.hidden;
          userMenu.hidden = !open;
          userBtn.setAttribute("aria-expanded", open ? "true" : "false");
          if (open) {
            var first = userMenu.querySelector("a");
            if (first) first.focus({ preventScroll: true });
          }
        });
        document.addEventListener("click", function(e){
          if (!userMenu.hidden && !e.target.closest("#jh_user")) closeUser();
        });
        document.addEventListener("keydown", function(e){
          if (!userMenu.hidden && (e.key === "ArrowDown" || e.key === "ArrowUp")) {
            e.preventDefault();
            var items = Array.prototype.slice.call(userMenu.querySelectorAll("a"));
            var i = items.indexOf(document.activeElement);
            var n = e.key === "ArrowDown" ? (i + 1) % items.length : (i - 1 + items.length) % items.length;
            items[n].focus();
          }
        });
      }

      // Helpers loader global (también usados por confirmaciones Swal).
      window.mostrarCarga = function(){
        var carga = document.getElementById("contenedor_carga");
        if (carga) carga.style.visibility = "visible";
      };
      window.ocultarCarga = function(){
        var carga = document.getElementById("contenedor_carga");
        if (carga) carga.style.visibility = "hidden";
      };
      // Al volver con back/forward (bfcache) nunca dejar el loader pegado.
      window.addEventListener("pageshow", function(){ window.ocultarCarga(); });

      // Anti doble-envío global: solo en POST que sí van a navegar.
      // Si otro handler hizo preventDefault (ej. confirmación Swal), NO mostrar
      // loader ni bloquear el botón; la vista lo hará solo tras confirmar.
      document.addEventListener("submit", function(e){
        if (e.defaultPrevented) return;
        var form = e.target;
        if (!form || form.tagName !== "FORM") return;
        var method = (form.method || form.getAttribute("method") || "get").toLowerCase();
        if (method !== "post") return;
        window.mostrarCarga();
        var btn = form.querySelector('button[type="submit"], button:not([type])');
        if (btn) btn.disabled = true;
      });
    })();
  </script>
  @stack('scripts')
  </body>
</html>
