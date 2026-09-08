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
            z-index: 1000; /* Menor que el sidebar */
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
            background: rgba(255, 255, 255, 0.95); /* Menos transparencia para que no se trasluzca lo de abajo */
            backdrop-filter: blur(15px);
            position: fixed;
            left: 0; top: 0;
            z-index: 2000; /* Superior a la barra móvil */
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 25px rgba(0,0,0,0.05);
        }

        /* Ocultar header del sidebar en móvil para evitar duplicidad */
        .sidebar-header {
            padding: 2.5rem 1.5rem;
            text-align: center;
        }
        
        .sidebar-header h1 {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--stellar-blue);
            margin: 0;
            letter-spacing: 1px;
        }

        /* Nav links */
        .sidebar-nav {
            flex-grow: 1;
            padding: 0 1rem;
        }
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.2rem;
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
        .sidebar-nav .nav-link i { width: 25px; margin-right: 12px; font-size: 1.1rem; }

        .sidebar-nav .nav-link:hover, 
        .sidebar-nav .nav-link.active {
            color: var(--stellar-blue) !important;
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        /* Panel de Usuario */
        .user-panel {
            padding: 1.5rem;
            background: rgba(14, 92, 173, 0.05);
            margin: 1rem;
            border-radius: 15px;
            text-align: center;
        }
        .user-panel span { display: block; color: var(--stellar-text); font-weight: 700; font-size: 0.9rem; }
        .user-panel .role { font-size: 0.7rem; color: var(--stellar-blue); text-transform: uppercase; font-weight: 800; }

        /* --- CONTENIDO --- */
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
            padding: 3rem;
            box-shadow: 0 15px 45px rgba(0,0,0,0.15);
            min-height: 85vh;
            color: var(--stellar-text);
        }

        /* RESPONSIVIDAD */
        @media (max-width: 991.98px) {
            #mobile-top-bar { display: flex; }
            
            /* Quitamos el header del sidebar en móvil para que no choque con la barra flotante */
            .sidebar-header { display: none; }
            
            #sidebar { 
                transform: translateX(-100%); 
                border-radius: 0 30px 30px 0; 
                padding-top: 2rem; /* Espacio para que los items no empiecen tan arriba */
            }
            #sidebar.active { transform: translateX(0); }
            
            #main-content { 
                margin-left: 0; 
                width: 100%; 
                padding: 1rem; 
                padding-top: 6rem; /* Espacio para que el contenido no quede bajo la barra flotante */
            }
            .main-card { padding: 1.5rem; }
            
            /* El panel de usuario sí lo mostramos en móvil dentro del menú */
            .user-panel { display: block !important; margin-top: 1rem; }
        }
    </style>
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