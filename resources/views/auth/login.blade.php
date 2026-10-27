<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso SIDUMSS - Estilo Stellar</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700" rel="stylesheet">
    <!-- Font Awesome para Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite('resources/css/views/auth/login.css')
</head>
<body>

    <div class="login-wrapper">
   

        <!-- Tarjeta de Login -->
        <div class="login-card card">
            <h2>SIDUMSS NORTE A</h2>
            
            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" placeholder="usuario@sidumss.com" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-stellar w-100">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Ingresar al Sistema
                </button>
            </form>
            
            @if($errors->any())
                <div class="alert alert-stellar mt-4 shadow-sm animate__animated animate__shakeX">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ $errors->first() }}
                </div>
            @endif
        </div>

        <div class="footer-text">
            &copy; {{ date('Y') }} Urbanización Norte Plan "A"
        </div>
    </div>

</body>
</html>