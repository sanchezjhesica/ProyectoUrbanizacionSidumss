<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIDUMSS - Panel de Administración</title>
    
    <!-- Bootstrap 5 y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
    
    <!-- SELECT2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    @vite('resources/css/layouts/admin.css')
    @stack('styles')
</head>
<body>

    <!-- BARRA SUPERIOR FLOTANTE (MÓVIL) -->
    <div id="mobile-top-bar">
        <h2 class="fw-bold">SIDUMSS</h2>
        <button class="btn border-0" id="sidebarCollapse">
            <i class="fas fa-bars fs-4 text-stellar-blue view-style-2ad2b3f699" ></i>
        </button>
    </div>

    <!-- SIDEBAR LATERAL -->
    <nav id="sidebar">
        <div class="sidebar-header d-none d-lg-block">
            <h1>SIDUMSS</h1>
            <p class="small text-muted text-uppercase tracking-wider">Administración</p>
        </div>

        <div class="sidebar-nav">
            <ul>
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i> Inicio
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Usuarios
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.viviendas.index') }}" class="nav-link {{ request()->routeIs('admin.viviendas.*') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Viviendas
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.lecturas.index') }}" class="nav-link {{ request()->routeIs('admin.lecturas.*') ? 'active' : '' }}">
                        <i class="fas fa-faucet"></i> Lecturas
                    </a>
                </li>

                <!-- SECCIÓN NUEVA: IMÁGENES RECIBIDAS -->
                <li>
                    <a href="{{ route('admin.reportes.imagenes') }}" class="nav-link {{ request()->routeIs('admin.reportes.imagenes') ? 'active' : '' }}">
                        <i class="fas fa-camera-retro"></i> QR RECIBIDOS
                    </a>
                </li>
                <!-- REEMPLAZAR ESTO: -->
                <li>
                    <a href="{{ route('admin.reservas.index') }}" class="nav-link {{ request()->routeIs('admin.reservas.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i> Solicitud de Reservas
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#menuReportes">
                        <i class="fas fa-chart-line"></i> Reportes
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.reportes.*') ? 'show' : '' }}" id="menuReportes">
                        <ul class="ms-3 list-unstyled">
                            <li><a class="nav-link view-style-a2b44b158b" href="{{ route('admin.reportes.general') }}" >General</a></li>
                            <li><a class="nav-link view-style-a2b44b158b" href="{{ route('admin.reportes.vivienda') }}" >Por Vivienda</a></li>
                            <li><a class="nav-link view-style-a2b44b158b" href="{{ route('admin.reportes.morosidad') }}" >Morosidad</a></li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="{{ route('admin.tarifas.edit') }}" class="nav-link {{ request()->routeIs('admin.tarifas.*') ? 'active' : '' }}">
                        <i class="fas fa-sliders-h"></i> Tarifas
                    </a>
                </li>
            </ul>
        </div>

        <div class="p-4 border-top mt-auto">
            <a href="#" class="nav-link text-danger fw-bold" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Salir
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </nav>

    <!-- CONTENIDO -->
    <main id="main-content">
        <div class="main-card">
            @yield('content')
        </div>
    </main>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#sidebarCollapse').on('click', function() {
                $('#sidebar').toggleClass('active');
            });

            $(document).on('click', function (e) {
                if ($(window).width() < 992) {
                    if (!$(e.target).closest('#sidebar, #sidebarCollapse').length) {
                        $('#sidebar').removeClass('active');
                    }
                }
            });

            $('.select2-stellar').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });
    </script>
    @stack('scripts')
</body>
</html>