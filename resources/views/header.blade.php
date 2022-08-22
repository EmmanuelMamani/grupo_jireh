<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8"/>
    <link rel="icon" href="{{secure_asset('img/logo1.ico')}}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta
      name="description"
      content="Web site created using create-react-app"
    />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/login.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gruppo&family=Space+Grotesk:wght@300&display=swap" rel="stylesheet">
    @yield('estilos')
    <title>Grupo Jireh</title>
  </head>
  <body>
    <header> <div id="sup"></div>
      <nav class="navbar">
        <div class="container-fluid" id="navbar">
          <a class="navbar-brand" href="#" id="cont_nav">
            <img src="{{asset('img/logo.png')}}" alt="" width="60" class="d-inline-block align-text-top">
           <span id="titulo">@yield("titulo")</span>
          </a>
          @yield("opciones")
        </div>
      </nav> <div id="inf"></div>
    </header>
    @yield("contenido")
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('registrar')=='ok')
    <script>
      Swal.fire({
      position: 'center',
      icon: 'success',
      title: 'Registro exitoso',
      showConfirmButton: false,
      timer: 1500
  })
</script>
@endif
@if (session('eliminar')=='ok')
<script>
  Swal.fire({
  position: 'center',
  icon: 'success',
  title: 'Registro eliminado',
  showConfirmButton: false,
  timer: 1500
  })
</script>
@endif

  </body>
</html>