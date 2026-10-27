<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIDUMSS - Portal Propietario</title>
    
    <!-- Bootstrap 5 y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
    
    @vite('resources/css/layouts/owner.css')
    @stack('styles')
</head>
<body>

    <!-- BARRA FLOTANTE MÓVIL (Se mantiene siempre arriba) -->
    <div id="mobile-top-bar">
        <h2 class="fw-bold">SIDUMSS</h2>
        <button class="btn border-0" id="sidebarCollapse">
            <i class="fas fa-bars fs-4 text-stellar-blue"></i>
        </button>
    </div>

    <!-- SIDEBAR -->
    <nav id="sidebar">
        <!-- Este header solo se verá en Laptop -->
        <div class="sidebar-header d-none d-lg-block">
            <h1>SIDUMSS</h1>
            <p class="small text-muted text-uppercase tracking-wider">Portal Propietario</p>
        </div>

        <div class="user-panel">
            <span class="role">Propietario</span>
            <span>{{ Auth::user()->nombre }} {{ Auth::user()->apellido_paterno }}</span>
        </div>

        <div class="sidebar-nav">
            <ul class="list-unstyled">
                <li>
                    <a href="{{ route('propietario.dashboard') }}" class="nav-link {{ request()->routeIs('propietario.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li>
                    <a href="{{ route('propietario.avisos') }}" class="nav-link {{ request()->routeIs('propietario.avisos') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar"></i> Mis Avisos
                    </a>
                </li>
                <li>
                    <a href="{{ route('propietario.reservas.index') }}" class="nav-link {{ request()->routeIs('propietario.reservas.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i> Reservas
                    </a>
                </li>
                <li>
                    <a href="{{ route('propietario.password.edit') }}" class="nav-link {{ request()->routeIs('propietario.password.edit') ? 'active' : '' }}">
                        <i class="fas fa-user-shield"></i> Seguridad
                    </a>
                </li>
            </ul>
        </div>

        <div class="p-4 mt-auto border-top">
            <a href="#" class="nav-link text-danger fw-bold" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Cerrar sesion
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <main id="main-content">
        <div class="main-card">
            @yield('content')
        </div>
    </main>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#sidebarCollapse').on('click', function() {
                $('#sidebar').toggleClass('active');
            });

            // Cerrar sidebar al hacer click fuera en móvil
            $(document).on('click', function (e) {
                if ($(window).width() < 992) {
                    if (!$(e.target).closest('#sidebar, #sidebarCollapse').length) {
                        $('#sidebar').removeClass('active');
                    }
                }
            });
        });
    </script>
</body>
</html>