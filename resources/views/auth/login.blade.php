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

    <style>
/* Estilo Base Stellar */
        body {
            background-color: #728156;
            background-image: linear-gradient(45deg, #79f1a4 15%, #0e5cad 85%);
            background-attachment: fixed;
            font-family: 'Source Sans Pro', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        /* Contenedor del Login */
        .login-wrapper {
            width: 100%;
            max-width: 450px;
            padding: 20px;
            text-align: center;
        }

        /* La Tarjeta Blanca */
        .login-card {
            background: #ffffff9a;
            border-radius: 15px;
            padding: 50px 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            border: none;
        }
        h2 {
            color: #201f1f;
            font-weight: 700;
            letter-spacing: 2px;
            margin-top: 20px;
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        /* Estilo de Inputs */
        .form-label {
            color: #807b7b;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
            text-align: left;
            margin-bottom: 8px;
        }

        .form-control {
            border: 1px solid #eeeeee94;
            border-radius: 8px;
            padding: 12px 15px;
            background: #fdfdfdab;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #5f4d93;
            box-shadow: 0 0 0 0.25rem rgba(95, 77, 147, 0.1);
            background: #fff;
        }

        /* Botón Stellar con Degradado */
        .btn-stellar {
            background: linear-gradient(45deg, #22349e 0%, #8183e6 100%);
            border: none;
            color: white;
            padding: 14px;
            border-radius: 50px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s ease;
            margin-top: 15px;
        }

        .btn-stellar:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(95, 77, 147, 0.3);
            color: white;
        }

        /* Alertas */
        .alert-stellar {
            background-color: #fff5f5;
            border: none;
            border-left: 4px solid #e37682;
            color: #c0392b;
            border-radius: 8px;
            font-size: 0.9rem;
            text-align: left;
        }

        .footer-text {
            margin-top: 30px;
            color: rgba(32, 31, 31, 0.8);
            font-size: 0.9rem;
            letter-spacing: 1px;
        }
</style>
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