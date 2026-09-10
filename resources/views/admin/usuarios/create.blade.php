@extends('layouts.admin')

@section('content')
<div class="usuarios-create-stellar">
    <!-- 1. CABECERA -->
    <div class="mb-4 mb-md-5 border-bottom pb-4">
        <h2 class="text-stellar-blue mb-0 fw-bold"><i class="fas fa-user-plus me-2"></i> Registrar Usuario</h2>
        <p class="text-muted mb-0">Cree una nueva cuenta de acceso, designe su rol y valide el correo electrónico.</p>
    </div>

    <!-- 2. ALERTA DE ERRORES -->
    @if ($errors->any())
        <div class="alert alert-stellar-danger mb-4 shadow-sm animate__animated animate__shakeX">
            <div class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i> Atención:</div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 3. TARJETA DEL FORMULARIO -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('admin.usuarios.store') }}" method="POST" id="form-registro">
                @csrf 
                
                <!-- Sección: Información Personal -->
                <div class="form-section mb-5">
                    <h5 class="text-stellar-blue fw-bold mb-4 border-bottom pb-2">
                        <i class="fas fa-id-card me-2"></i>Información Personal
                    </h5>
                    <div class="row g-3 g-md-4">
                        <div class="col-md-4">
                            <label class="form-label-stellar">Nombre(s)</label>
                            <input type="text" name="nombre" class="form-control form-stellar" value="{{ old('nombre') }}" placeholder="Ej. Juan" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-stellar">Apellido Paterno</label>
                            <input type="text" name="apellido_paterno" class="form-control form-stellar" value="{{ old('apellido_paterno') }}" placeholder="Ej. Pérez">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-stellar">Apellido Materno</label>
                            <input type="text" name="apellido_materno" class="form-control form-stellar" value="{{ old('apellido_materno') }}" placeholder="Ej. Mamani">
                        </div>
                    </div>
                </div>

                <!-- Sección: Documentación y Rol del Usuario -->
                <div class="form-section mb-5">
                    <h5 class="text-stellar-blue fw-bold mb-4 border-bottom pb-2">
                        <i class="fas fa-address-book me-2"></i>Documentación y Rol de Usuario
                    </h5>
                    <div class="row g-3 g-md-4">
                        <div class="col-md-4">
                            <label class="form-label-stellar">Cédula de Identidad (CI)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-fingerprint text-muted"></i></span>
                                <input type="text" name="ci" class="form-control form-stellar border-start-0" value="{{ old('ci') }}" placeholder="Número de documento" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-stellar">Teléfono / Celular</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-mobile-alt text-muted"></i></span>
                                <input type="text" name="telefono" class="form-control form-stellar border-start-0" value="{{ old('telefono') }}" placeholder="Ej. 70000000">
                            </div>
                        </div>

                        <!-- NUEVO CAMPO: SELECCIÓN DE ROL -->
                        <div class="col-md-4">
                            <label class="form-label-stellar">Rol de Usuario</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-user-tag text-muted"></i></span>
                                <select name="id_rol" class="form-select form-stellar border-start-0" required>
                                    <option value="" disabled {{ old('id_rol') ? '' : 'selected' }}>Seleccione un rol...</option>
                                    @if(isset($roles) && count($roles) > 0)
                                        @foreach($roles as $rol)
                                            <option value="{{ $rol->id_rol }}" {{ old('id_rol') == $rol->id_rol ? 'selected' : '' }}>
                                                {{ $rol->nombre_rol }}
                                            </option>
                                        @endforeach
                                    @else
                                        {{-- Opciones por defecto según tu tabla roles --}}
                                        <option value="1" {{ old('id_rol') == 1 ? 'selected' : '' }}>Administrador</option>
                                        <option value="2" {{ old('id_rol') == 2 ? 'selected' : '' }}>Operador</option>
                                        <option value="3" {{ old('id_rol') == 3 ? 'selected' : '' }}>Propietario</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Acceso y Validación de Correo -->
                <div class="form-section mb-5">
                    <h5 class="text-stellar-blue fw-bold mb-4 border-bottom pb-2">
                        <i class="fas fa-key me-2"></i>Credenciales y Validación de Correo
                    </h5>
                    
                    <div class="row g-3 g-md-4 align-items-end mb-4">
                        <div class="col-md-8">
                            <label class="form-label-stellar">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" id="input-email" class="form-control form-stellar border-start-0" value="{{ old('email') }}" placeholder="usuario@correo.com" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="button" id="btn-enviar-codigo" class="btn btn-outline-primary w-100 py-2 fw-bold rounded-pill">
                                <i class="fas fa-paper-plane me-2"></i> Enviar Código
                            </button>
                        </div>
                    </div>

                    <!-- Input de Código (Oculto hasta que se envía) -->
                    <div id="contenedor-codigo" class="row g-3 g-md-4 d-none mb-4 animate__animated animate__fadeIn">
                        <div class="col-md-6">
                            <label class="form-label-stellar text-success"><i class="fas fa-shield-alt me-1"></i> Ingrese el Código de 6 Dígitos</label>
                            <input type="text" name="codigo_verificacion" id="input-codigo" class="form-control form-stellar fw-bold text-center fs-4" placeholder="123456" maxlength="6">
                            <small class="text-muted mt-1 d-block">Se ha enviado un código de prueba a su correo.</small>
                        </div>
                    </div>

                    <div class="row g-3 g-md-4">
                        <div class="col-md-6">
                            <label class="form-label-stellar">Contraseña Inicial</label>
                            <input type="password" name="password" class="form-control form-stellar" placeholder="Mínimo 8 caracteres" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-stellar">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control form-stellar" placeholder="Repita la contraseña" required>
                        </div>
                    </div>
                </div>

                <!-- 4. BOTONES DE ACCIÓN -->
                <div class="pt-4 border-top d-flex flex-column flex-md-row gap-3 justify-content-md-end">
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary rounded-pill px-4 d-flex align-items-center justify-content-center order-2 order-md-1">
                        <i class="fas fa-arrow-left me-2"></i> Cancelar
                    </a>
                    <button type="submit" id="btn-guardar" class="btn btn-stellar-blue btn-lg px-5 shadow-sm order-1 order-md-2">
                        <i class="fas fa-save me-2"></i> Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    :root {
        --stellar-blue: #0e5cad;
        --stellar-button: linear-gradient(45deg, #22349e 0%, #8183e6 100%);
        --stellar-input-focus: rgba(14, 92, 173, 0.1);
    }

    .text-stellar-blue { color: var(--stellar-blue); }
    .form-label-stellar { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; color: #777; margin-bottom: 8px; display: block; }

    .form-stellar {
        border: 1px solid #e0e0e0; border-radius: 10px; padding: 12px 15px; background-color: #fdfdfd; transition: all 0.3s ease; font-size: 0.95rem;
    }
    .form-stellar:focus { border-color: var(--stellar-blue); background-color: #fff; box-shadow: 0 0 0 0.25rem var(--stellar-input-focus); }
    .input-group-text { border-radius: 10px 0 0 10px !important; border: 1px solid #e0e0e0; color: #aaa; }

    .btn-stellar-blue {
        background: var(--stellar-button); color: white !important; border-radius: 50px; font-weight: 700; border: none; padding: 14px 35px; transition: 0.3s; text-transform: uppercase; letter-spacing: 1px; font-size: 0.85rem;
    }
    .btn-stellar-blue:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(34, 52, 158, 0.3); }

    .alert-stellar-danger { background-color: #fff5f5; border-left: 5px solid #e74c3c; color: #c0392b; padding: 20px; border-radius: 12px; }
    .rounded-4 { border-radius: 1.25rem !important; }

    @media (max-width: 767.98px) {
        .card-body { padding: 1.5rem !important; }
        .btn-stellar-blue, .btn-outline-secondary { width: 100%; }
        h2 { font-size: 1.5rem; }
    }
</style>

<!-- SCRIPT DE ENVÍO DE CÓDIGO -->
<script>
    document.getElementById('btn-enviar-codigo').addEventListener('click', function() {
        let emailField = document.getElementById('input-email');
        let email = emailField.value;
        let btn = this;

        if(!email) {
            alert('Por favor, ingrese un correo electrónico primero.');
            emailField.focus();
            return;
        }

        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Enviando...';
        btn.disabled = true;

        fetch("{{ route('admin.usuarios.enviar_codigo') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('contenedor-codigo').classList.remove('d-none');
                btn.innerHTML = '<i class="fas fa-check me-2"></i> ¡Código Enviado!';
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-success');
                alert(data.message);
            } else {
                alert('Hubo un error: ' + data.message);
                btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Enviar Código';
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error de red al intentar enviar el correo.');
            btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Enviar Código';
            btn.disabled = false;
        });
    });
</script>
@endsection