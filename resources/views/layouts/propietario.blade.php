<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URBANIZACIÓN SIDUMSS NORTE A - Portal Propietario</title>
    
    <!-- Bootstrap 5 y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
    
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
            overflow-x: hidden;
        }

        /* --- BARRA MÓVIL (ISLA FLOTANTE) --- */
        #mobile-top-bar {
            display: none;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 0.75rem 1.25rem;
            position: fixed;
            top: 12px; left: 12px; right: 12px;
            z-index: 1050;
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            justify-content: space-between;
            align-items: center;
        }

        #mobile-top-bar h2 {
            font-size: 0.95rem;
            font-weight: 800;
            letter-spacing: 1px;
            margin: 0;
            color: var(--stellar-blue);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 70%;
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
            padding: 1.5rem 1.2rem 0.5rem 1.2rem;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-header h1 {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--stellar-blue);
            margin: 0;
            letter-spacing: 1px;
            line-height: 1.3;
        }

        /* Panel de Usuario */
        .user-panel {
            padding: 1rem;
            background: rgba(14, 92, 173, 0.05);
            margin: 0.8rem 1rem;
            border-radius: 12px;
            text-align: center;
            flex-shrink: 0;
        }
        .user-panel span { display: block; color: var(--stellar-text); font-weight: 700; font-size: 0.85rem; }
        .user-panel .role { font-size: 0.65rem; color: var(--stellar-blue); text-transform: uppercase; font-weight: 800; letter-spacing: 0.5px; }

        /* Nav links con scroll propio */
        .sidebar-nav {
            flex-grow: 1;
            padding: 0 1rem;
            overflow-y: auto;
            scrollbar-width: thin;
        }
        
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            color: var(--stellar-muted) !important;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 4px;
            transition: var(--transition);
        }
        
        .sidebar-nav .nav-link i { width: 25px; margin-right: 10px; font-size: 1.05rem; text-align: center; }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: var(--stellar-blue) !important;
            background: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transform: translateX(4px);
        }

        /* Footer del Sidebar Fijo (Cerrar Sesión) */
        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(0,0,0,0.06);
            background: rgba(255, 255, 255, 0.9);
            flex-shrink: 0;
        }

        /* --- CONTENIDO PRINCIPAL --- */
        #main-content {
            flex-grow: 1;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 2.5rem;
            transition: var(--transition);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: clamp(1.2rem, 3vw, 2.5rem);
            box-shadow: 0 15px 45px rgba(0,0,0,0.12);
            width: 100%;
            flex-grow: 1;
            color: var(--stellar-text);
        }

        /* RESPONSIVIDAD AMPLIADA (TABLETS Y MÓVILES < 1200px) */
        @media (max-width: 1199.98px) {
            #mobile-top-bar { display: flex; }
            .sidebar-header { display: none; } /* Ocultamos título largo para dejar espacio al panel de usuario */

            #sidebar {
                transform: translateX(-100%);
                border-radius: 0 25px 25px 0;
                padding-top: 4.2rem; /* Espacio para la barra flotante superior */
            }
            #sidebar.active { 
                transform: translateX(0); 
                box-shadow: 10px 0 40px rgba(0,0,0,0.2);
            }

            #main-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
                padding-top: 5.5rem; /* Evita que el contenido quede detrás de la barra flotante */
            }

            /* Aseguramos que el panel de usuario luzca bien en móvil */
            .user-panel { display: block !important; margin-top: 0.5rem; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- BARRA FLOTANTE MÓVIL Y TABLET -->
    <div id="mobile-top-bar">
        <h2>SIDUMSS NORTE A</h2>
        <button class="btn border-0 p-1" id="sidebarCollapse" aria-label="Abrir menú">
            <i class="fas fa-bars fs-4 text-stellar-blue"></i>
        </button>
    </div>

    <!-- SIDEBAR -->
    <nav id="sidebar">
        <!-- Header visible solo en Escritorio (Laptops/PCs grandes) -->
        <div class="sidebar-header d-none d-xl-block">
            <h1>URBANIZACIÓN<br>SIDUMSS NORTE A</h1>
            <p class="small text-muted text-uppercase tracking-wider mt-1 mb-0">Portal Propietario</p>
        </div>

        <!-- Panel de Usuario visible siempre -->
        <div class="user-panel">
            <span class="role">Propietario</span>
            <span>{{ Auth::user()->nombre }} {{ Auth::user()->apellido_paterno }}</span>
        </div>

        <!-- Enlaces con scroll independiente -->
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

        <!-- Botón Cerrar Sesión fijo en la parte inferior -->
        <div class="sidebar-footer">
            <a href="#" class="nav-link text-danger fw-bold m-0" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
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
            $('#sidebarCollapse').on('click', function(e) {
                e.stopPropagation();
                $('#sidebar').toggleClass('active');
            });

            // Cerrar sidebar al hacer clic fuera de él en pantallas pequeñas/medianas
            $(document).on('click', function (e) {
                if (window.innerWidth < 1200) {
                    if (!$(e.target).closest('#sidebar, #mobile-top-bar').length) {
                        $('#sidebar').removeClass('active');
                    }
                }
            });
        });
    </script>
</body>
</html>