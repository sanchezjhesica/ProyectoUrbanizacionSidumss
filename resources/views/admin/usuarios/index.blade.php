@extends('layouts.admin')

@section('content')
<div class="usuarios-stellar">
    <!-- 1. ENCABEZADO RESPONSIVO (Colapsa en móvil, en fila en PC) -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-3 mb-md-4 border-bottom pb-3 gap-3">
        <div>
            <h2 class="text-stellar-blue mb-1 fw-bold fs-4 fs-md-3">
                <i class="fas fa-users-cog me-2"></i> Gestión de Usuarios
            </h2>
            <p class="text-muted small mb-0">Administre los perfiles y permisos de acceso al sistema.</p>
        </div>
        
        <!-- BARRA DE BÚSQUEDA Y BOTÓN NUEVO -->
        <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-lg-auto">
            <!-- Input del buscador con botón limpiar -->
            <div class="input-group search-box-stellar flex-grow-1">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                <input type="text" id="input-buscar" class="form-control border-start-0 ps-0" placeholder="Buscar CI, nombre o correo..." autocomplete="off">
                <button class="btn btn-white border border-start-0 text-muted d-none" type="button" id="btn-limpiar-busqueda">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            <!-- Botón Nuevo Usuario -->
            <a href="{{ route('admin.usuarios.create') }}" class="btn btn-stellar shadow-sm text-nowrap text-center">
                <i class="fas fa-plus-circle me-1"></i> Nuevo Usuario
            </a>
        </div>
    </div>

    <!-- 2. MENSAJE DE ÉXITO -->
    @if(session('success'))
        <div class="alert alert-stellar alert-dismissible fade show mb-3 mb-md-4 border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 3. TABLA RESPONSIVA ADAPTABLE -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive m-0">
                <table class="table table-hover align-middle mb-0 w-100" id="tabla-usuarios">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <!-- En móvil el CI se integra en la tarjeta de Usuario -->
                            <th class="ps-3 ps-md-4 py-3 uppercase-tracking d-none d-md-table-cell" style="width: 140px;">Identidad / CI</th>
                            <th class="ps-3 ps-md-3 py-3 uppercase-tracking">Usuario</th>
                            <th class="py-3 uppercase-tracking d-none d-lg-table-cell">Correo Electrónico</th>
                            <th class="py-3 pe-3 pe-md-4 text-end uppercase-tracking" style="width: 130px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-usuarios">
                        @forelse($usuarios as $u)
                        <tr class="fila-usuario">
                            <!-- CI: Visible solo de tablets en adelante -->
                            <td class="ps-3 ps-md-4 d-none d-md-table-cell">
                                <span class="badge bg-blue-soft text-stellar-blue px-2.5 py-1.5 fw-semibold font-monospace">
                                    {{ $u->ci }}
                                </span>
                            </td>

                            <!-- DATOS DEL USUARIO (Se adapta fluidamente) -->
                            <td class="ps-3 ps-md-3">
                                <div class="d-flex align-items-center">
                                    <!-- Avatar (un poco más pequeño en móviles) -->
                                    <div class="avatar-stellar me-2 me-md-3">
                                        {{ strtoupper(substr($u->nombre, 0, 1)) }}{{ strtoupper(substr($u->apellido_paterno ?? '', 0, 1)) }}
                                    </div>
                                    
                                    <div class="lh-sm">
                                        <!-- Nombre Completo -->
                                        <div class="fw-bold text-dark text-break">
                                            {{ $u->nombre }} {{ $u->apellido_paterno }}
                                            @if($u->apellido_materno)
                                                <span class="text-muted fw-normal small d-none d-sm-inline">{{ $u->apellido_materno }}</span>
                                            @endif
                                        </div>
                                        
                                        <!-- EN MÓVILES: CI y Correo juntos debajo del nombre -->
                                        <div class="d-md-none mt-1">
                                            <span class="badge bg-blue-soft text-stellar-blue font-monospace px-1.5 py-0.5 me-1" style="font-size: 0.7rem;">
                                                CI: {{ $u->ci }}
                                            </span>
                                        </div>
                                        <div class="small text-muted d-lg-none mt-0.5 text-break">
                                            <i class="far fa-envelope me-1"></i>{{ $u->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- CORREO ELECTRÓNICO (Solo visible en pantallas grandes) -->
                            <td class="d-none d-lg-table-cell text-muted">
                                <div class="d-flex align-items-center">
                                    <i class="far fa-envelope text-muted me-2"></i>
                                    <span class="text-truncate" style="max-width: 250px;">{{ $u->email }}</span>
                                </div>
                            </td>

                            <!-- ACCIONES DE GESTIÓN (Siempre visibles y compactas) -->
                            <td class="pe-3 pe-md-4 text-end">
                                <div class="d-inline-flex align-items-center justify-content-end gap-1">
                                    <!-- RESTABLECER CLAVE -->
                                    <form action="{{ route('admin.usuarios.reset', $u->id_usuario) }}" method="POST" onsubmit="return confirm('¿Restablecer contraseña a sidumss123?')">
                                        @csrf
                                        <button type="submit" class="btn-action-stellar btn-reset-stellar" title="Restablecer Clave">
                                            <i class="fas fa-key"></i>
                                            <span class="d-none d-xl-inline ms-1">Clave</span>
                                        </button>
                                    </form>

                                    <!-- EDITAR -->
                                    <a href="{{ route('admin.usuarios.edit', $u->id_usuario) }}" class="btn-action-stellar btn-edit-stellar" title="Editar Perfil">
                                        <i class="fas fa-pen"></i>
                                        <span class="d-none d-xl-inline ms-1">Editar</span>
                                    </a>

                                    <!-- ELIMINAR -->
                                    <form action="{{ route('admin.usuarios.destroy', $u->id_usuario) }}" method="POST" onsubmit="return confirm('¿Eliminar usuario definitivamente?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-stellar btn-delete-stellar" title="Eliminar Usuario">
                                            <i class="fas fa-trash-alt"></i>
                                            <span class="d-none d-xl-inline ms-1">Borrar</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-users-slash fa-2x mb-3 d-block opacity-50"></i>
                                No hay usuarios registrados en el sistema.
                            </td>
                        </tr>
                        @endforelse

                        <!-- MENSAJE DE BÚSQUEDA SIN COINCIDENCIAS -->
                        <tr id="sin-coincidencias" class="d-none">
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-search fa-2x mb-3 d-block text-secondary opacity-50"></i>
                                No se encontraron usuarios que coincidan con la búsqueda.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --stellar-blue: #0e5cad;
        --stellar-grad: linear-gradient(135deg, #79f1a4 0%, #0e5cad 100%);
        --stellar-button: linear-gradient(45deg, #22349e 0%, #8183e6 100%);
    }

    .text-stellar-blue { color: var(--stellar-blue); }
    .bg-blue-soft { background-color: rgba(14, 92, 173, 0.08); color: var(--stellar-blue); }
    .alert-stellar { background-color: #e8f5e9; color: #2e7d32; border-left: 5px solid #2e7d32; }
    
    .uppercase-tracking {
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 700;
        color: #718096;
    }

    /* Buscador responsivo */
    .search-box-stellar {
        width: 100%;
        max-width: 320px;
    }
    @media (max-width: 575.98px) {
        .search-box-stellar {
            max-width: 100%; /* Ocupa todo el ancho en móviles */
        }
    }
    .search-box-stellar .input-group-text {
        border-radius: 50px 0 0 50px;
        border-color: #e2e8f0;
        background-color: #fff;
    }
    .search-box-stellar input {
        border-radius: 0 50px 50px 0;
        border-color: #e2e8f0;
        font-size: 0.85rem;
    }
    .search-box-stellar input:focus {
        border-color: var(--stellar-blue);
        box-shadow: none;
    }

    /* Avatar adaptable */
    .avatar-stellar {
        width: 36px;
        height: 36px;
        min-width: 36px;
        background: var(--stellar-grad);
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.75rem;
        flex-shrink: 0;
    }

    /* Botones de acción ultra compactos */
    .btn-action-stellar {
        height: 32px;
        min-width: 32px;
        padding: 0 8px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: all 0.2s ease-in-out;
        border: 1px solid transparent;
        text-decoration: none;
    }

    .btn-reset-stellar {
        background-color: #fffaf0; border-color: #fbd38d; color: #d69e2e;
    }
    .btn-reset-stellar:hover { background-color: #d69e2e; color: white; }

    .btn-edit-stellar {
        background-color: #ebf8ff; border-color: #bee3f8; color: #3182ce;
    }
    .btn-edit-stellar:hover { background-color: #3182ce; color: white; }

    .btn-delete-stellar {
        background-color: #fff5f5; border-color: #fed7d7; color: #e53e3e;
    }
    .btn-delete-stellar:hover { background-color: #e53e3e; color: white; }

    /* Botón Nuevo */
    .btn-stellar {
        background: var(--stellar-button);
        color: white !important;
        border-radius: 50px;
        padding: 8px 18px;
        font-weight: 600;
        font-size: 0.82rem;
        border: none;
        transition: 0.2s;
    }
    .btn-stellar:hover {
        opacity: 0.92;
        transform: translateY(-1px);
    }

    /* Scrollbar invisible o suave si se necesita */
    .table-responsive {
        border: none;
        scrollbar-width: thin;
    }
</style>

<!-- SCRIPT DE FILTRADO ULTRA RÁPIDO Y MULTICAMPO -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputBuscar = document.getElementById('input-buscar');
        const btnLimpiar = document.getElementById('btn-limpiar-busqueda');
        const filas = document.querySelectorAll('.fila-usuario');
        const filaSinCoincidencias = document.getElementById('sin-coincidencias');

        inputBuscar.addEventListener('input', function () {
            const termino = this.value.toLowerCase().trim();
            let visibles = 0;

            // Mostrar u ocultar botón de limpiar (X)
            if (termino.length > 0) {
                btnLimpiar.classList.remove('d-none');
            } else {
                btnLimpiar.classList.add('d-none');
            }

            filas.forEach(fila => {
                // Al buscar sobre el textContent completo de la fila,
                // coincide con CI, nombre o correo tanto en móvil como en PC.
                const textoCompleto = fila.textContent.toLowerCase();

                if (textoCompleto.includes(termino)) {
                    fila.style.display = '';
                    visibles++;
                } else {
                    fila.style.display = 'none';
                }
            });

            // Mensaje si no hay resultados
            if (visibles === 0 && filas.length > 0) {
                filaSinCoincidencias.classList.remove('d-none');
            } else {
                filaSinCoincidencias.classList.add('d-none');
            }
        });

        // Limpiar búsqueda
        btnLimpiar.addEventListener('click', function () {
            inputBuscar.value = '';
            inputBuscar.dispatchEvent(new Event('input'));
            inputBuscar.focus();
        });
    });
</script>
@endsection