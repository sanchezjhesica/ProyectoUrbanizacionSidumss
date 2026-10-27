<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIDUMSS - Panel Operador</title>
    
    <!-- Bootstrap 5 y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
    
    @vite('resources/css/layouts/operator.css')
    @stack('styles')
</head>
<body>

    <!-- BARRA FLOTANTE MÓVIL -->
    <div id="mobile-top-bar">
        <h2 class="fw-bold">SIDUMSS NORTE A</h2>
        <button class="btn border-0" id="sidebarCollapse">
            <i class="fas fa-bars fs-4 text-stellar-blue view-style-2ad2b3f699" ></i>
        </button>
    </div>

    <!-- SIDEBAR OPERADOR -->
    <nav id="sidebar">
        <div class="sidebar-header d-none d-lg-block">
            <h1>SIDUMSS NORTE A</h1>
            <p class="small text-muted text-uppercase tracking-wider">Panel de trabajo del Operador</p>
        </div>

        <div class="user-panel">
            <span class="role"><i class="fas fa-hard-hat me-1"></i> Operador</span>
            <span class="name">{{ Auth::user()->nombre }}</span>
        </div>

        <div class="sidebar-nav">
            <ul class="list-unstyled">
                <li>
                    <a href="{{ route('operador.dashboard') }}" class="nav-link {{ request()->routeIs('operador.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i> Inicio
                    </a>
                </li>
                <li>
                    <a href="{{ route('operador.lecturas.crear') }}" class="nav-link {{ request()->routeIs('operador.lecturas.*') ? 'active' : '' }}">
                        <i class="fas fa-faucet"></i> Registrar Lectura
                    </a>
                </li>
            </ul>
        </div>

        <!-- Botón Salir al Final -->
        <div class="p-4 mt-auto border-top">
            <a href="#" class="nav-link text-danger fw-bold" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
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
            // Abrir Sidebar
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
    @stack('scripts')
</body>
</html>