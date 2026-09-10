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

    <style>
        :root {
            --stellar-grad: linear-gradient(45deg, #79f1a4 15%, #0e5cad 85%);
            --stellar-blue: #0e5cad;
            --stellar-text: #201f1f;
            --stellar-muted: #807b7b;
            --sidebar-width: 280px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background-image: var(--stellar-grad);
            background-attachment: fixed;
            background-size: cover;
            font-family: 'Source Sans Pro', sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
        }

        /* --- BARRA MÓVIL (ISLA FLOTANTE) --- */
        #mobile-top-bar {
            display: none;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 0.6rem 1.5rem;
            position: fixed;
            top: 15px; left: 15px; right: 15px;
            z-index: 1000;
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            justify-content: space-between;
            align-items: center;
        }

        #mobile-top-bar h2 {
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: 2px;
            margin: 0;
            color: var(--stellar-blue);
        }

        /* --- SIDEBAR LATERAL --- */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(15px);
            position: fixed;
            left: 0; top: 0;
            z-index: 2000;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 25px rgba(0,0,0,0.05);
        }

        .sidebar-header {
            padding: 2.5rem 1.5rem;
            text-align: center;
        }
        .sidebar-header h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--stellar-blue);
            letter-spacing: 2px;
            margin: 0;
        }

        .sidebar-nav {
            flex-grow: 1;
            padding: 0 1rem;
            overflow-y: auto;
        }

        .sidebar-nav ul { list-style: none; padding: 0; margin: 0; }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            padding: 0.9rem 1.2rem;
            color: var(--stellar-muted) !important;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 5px;
            transition: var(--transition);
        }

        .sidebar-nav .nav-link i { width: 25px; margin-right: 12px; text-align: center; font-size: 1.1rem; }

        .sidebar-nav .nav-link:hover, 
        .sidebar-nav .nav-link.active {
            color: var(--stellar-blue) !important;
            background: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transform: translateX(5px);
        }

        /* Dropdown interno */
        .sidebar-nav .dropdown-menu {
            background: rgba(0,0,0,0.03);
            border: none;
            margin-left: 1.5rem;
            border-radius: 10px;
        }

        /* --- CONTENIDO PRINCIPAL --- */
        #main-content {
            flex-grow: 1;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 2.5rem;
            transition: var(--transition);
        }

        .main-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: clamp(1.5rem, 4vw, 3rem);
            box-shadow: 0 15px 45px rgba(0,0,0,0.15);
            min-height: 80vh;
            color: var(--stellar-text);
        }

        /* --- RESPONSIVIDAD --- */
        @media (max-width: 991.98px) {
            #mobile-top-bar { display: flex; }
            .sidebar-header { display: none; }
            #sidebar {
                transform: translateX(-100%);
                border-radius: 0 30px 30px 0;
                padding-top: 2rem;
            }
            #sidebar.active {
                transform: translateX(0);
            }
            #main-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
                padding-top: 6rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- BARRA SUPERIOR FLOTANTE (MÓVIL) -->
    <div id="mobile-top-bar">
        <h2 class="fw-bold">SIDUMSS</h2>
        <button class="btn border-0" id="sidebarCollapse">
            <i class="fas fa-bars fs-4 text-stellar-blue" style="color: var(--stellar-blue);"></i>
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
                        <i class="fas fa-camera-retro"></i> Imágenes Recibidas
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
                            <li><a class="nav-link" href="{{ route('admin.reportes.general') }}" style="font-size: 0.75rem;">General</a></li>
                            <li><a class="nav-link" href="{{ route('admin.reportes.vivienda') }}" style="font-size: 0.75rem;">Por Vivienda</a></li>
                            <li><a class="nav-link" href="{{ route('admin.reportes.morosidad') }}" style="font-size: 0.75rem;">Morosidad</a></li>
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