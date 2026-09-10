@extends('layouts.admin')

@section('content')
<div class="reservas-stellar">
    <!-- 1. ENCABEZADO -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-3 mb-md-4 border-bottom pb-3 gap-3">
        <div>
            <h2 class="text-stellar-blue mb-1 fw-bold fs-4 fs-md-3">
                <i class="fas fa-calendar-check me-2"></i> Solicitudes de Reservas
            </h2>
            <p class="text-muted small mb-0">Gestione y valide las peticiones de áreas comunes realizadas por los residentes.</p>
        </div>
        
        <!-- BUSCADOR -->
        <div class="input-group search-box-stellar">
            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
            <input type="text" id="input-buscar" class="form-control border-start-0 ps-0" placeholder="Buscar residente, área, fecha..." autocomplete="off">
            <button class="btn btn-white border border-start-0 text-muted d-none" type="button" id="btn-limpiar-busqueda">
                <i class="fas fa-times-circle"></i>
            </button>
        </div>
    </div>

    <!-- 2. ALERTAS DE ÉXITO O RECHAZO -->
    @if(session('success'))
        <div class="alert alert-stellar alert-dismissible fade show mb-3 border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3 border-0 shadow-sm" role="alert">
            <i class="fas fa-times-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- 3. TABLA DE RESERVAS -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive m-0">
                <table class="table table-hover align-middle mb-0 w-100" id="tabla-reservas">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="ps-3 ps-md-4 py-3 uppercase-tracking">Residente</th>
                            <th class="py-3 uppercase-tracking">Área Común</th>
                            <th class="py-3 uppercase-tracking text-center">Fecha Reservada</th>
                            <th class="py-3 uppercase-tracking text-center">Costo</th>
                            <th class="py-3 uppercase-tracking text-center">Estado</th>
                            <th class="py-3 pe-3 pe-md-4 text-end uppercase-tracking" style="min-width: 140px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-reservas">
                        @forelse($reservas as $r)
                        <tr class="fila-reserva">
                            <!-- Residente -->
                            <td class="ps-3 ps-md-4">
                                <div class="fw-bold text-dark text-nowrap">
                                    {{ $r->nombre }} {{ $r->apellido_paterno }}
                                </div>
                                <div class="small text-muted font-monospace">
                                    CI: {{ $r->ci }} @if($r->telefono) | Cel: {{ $r->telefono }} @endif
                                </div>
                            </td>

                            <!-- Área Común -->
                            <td>
                                <span class="fw-semibold text-stellar-blue text-nowrap">
                                    <i class="fas fa-map-marker-alt me-1 text-danger opacity-75"></i> {{ $r->nombre_area }}
                                </span>
                            </td>

                            <!-- Fecha Reservada -->
                            <td class="text-center text-nowrap">
                                <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-semibold">
                                    <i class="far fa-calendar-alt me-1 text-primary"></i>
                                    {{ date('d/m/Y', strtotime($r->fecha_reserva)) }}
                                </span>
                            </td>

                            <!-- Costo -->
                            <td class="text-center fw-bold text-dark text-nowrap">
                                Bs. {{ number_format($r->costo_pactado, 2) }}
                            </td>

                            <!-- Estado con Badge Dinámico -->
                            <td class="text-center text-nowrap">
                                @if($r->estado_reserva == 'Aceptado')
                                    <span class="badge bg-success-soft text-success px-3 py-2 rounded-pill fw-bold">
                                        <i class="fas fa-check-circle me-1"></i> Aceptado
                                    </span>
                                @elseif($r->estado_reserva == 'Denegado')
                                    <span class="badge bg-danger-soft text-danger px-3 py-2 rounded-pill fw-bold">
                                        <i class="fas fa-times-circle me-1"></i> Denegado
                                    </span>
                                @else
                                    <span class="badge bg-warning-soft text-warning px-3 py-2 rounded-pill fw-bold">
                                        <i class="fas fa-clock me-1"></i> Pendiente
                                    </span>
                                @endif
                            </td>

                            <!-- Botones de Aceptar / Denegar -->
                            <td class="pe-3 pe-md-4 text-end text-nowrap">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <!-- ACEPTAR -->
                                    <form action="{{ route('admin.reservas.aceptar', $r->id_reserva) }}" method="POST" onsubmit="return confirm('¿Desea ACEPTAR esta reserva?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-action-reserva btn-accept" title="Aceptar Reserva" {{ $r->estado_reserva == 'Aceptado' ? 'disabled' : '' }}>
                                            <i class="fas fa-check"></i> <span class="d-none d-xl-inline ms-1">Aceptar</span>
                                        </button>
                                    </form>

                                    <!-- DENEGAR -->
                                    <form action="{{ route('admin.reservas.denegar', $r->id_reserva) }}" method="POST" onsubmit="return confirm('¿Desea DENEGAR esta reserva?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-action-reserva btn-reject" title="Denegar Reserva" {{ $r->estado_reserva == 'Denegado' ? 'disabled' : '' }}>
                                            <i class="fas fa-times"></i> <span class="d-none d-xl-inline ms-1">Denegar</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-calendar-times fa-2x mb-3 d-block opacity-50"></i>
                                No hay solicitudes de reserva registradas.
                            </td>
                        </tr>
                        @endforelse

                        <tr id="sin-coincidencias" class="d-none">
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-search fa-2x mb-3 d-block text-secondary opacity-50"></i>
                                No se encontraron solicitudes coincidentes.
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
        --stellar-button: linear-gradient(45deg, #22349e 0%, #8183e6 100%);
    }

    .text-stellar-blue { color: var(--stellar-blue); }
    .alert-stellar { background-color: #e8f5e9; color: #2e7d32; border-left: 5px solid #2e7d32; }

    .uppercase-tracking {
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 700;
        color: #718096;
    }

    /* Badges Suaves */
    .bg-success-soft { background-color: rgba(46, 125, 50, 0.12); }
    .bg-danger-soft { background-color: rgba(229, 62, 62, 0.12); }
    .bg-warning-soft { background-color: rgba(243, 156, 18, 0.15); color: #d68910 !important; }

    /* Botones de acción */
    .btn-action-reserva {
        height: 32px;
        padding: 0 10px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: all 0.2s;
    }
    .btn-accept {
        background-color: #e8f5e9; border: 1px solid #c8e6c9; color: #2e7d32;
    }
    .btn-accept:hover:not(:disabled) {
        background-color: #2e7d32; color: white;
    }
    .btn-reject {
        background-color: #ffebee; border: 1px solid #ffcdd2; color: #c62828;
    }
    .btn-reject:hover:not(:disabled) {
        background-color: #c62828; color: white;
    }
    .btn-action-reserva:disabled {
        opacity: 0.35;
        cursor: not-allowed;
    }

    /* Buscador */
    .search-box-stellar { width: 100%; max-width: 320px; }
    .search-box-stellar .input-group-text { border-radius: 50px 0 0 50px; border-color: #e2e8f0; }
    .search-box-stellar input { border-radius: 0 50px 50px 0; border-color: #e2e8f0; font-size: 0.85rem; }
    .search-box-stellar input:focus { border-color: var(--stellar-blue); box-shadow: none; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputBuscar = document.getElementById('input-buscar');
        const btnLimpiar = document.getElementById('btn-limpiar-busqueda');
        const filas = document.querySelectorAll('.fila-reserva');
        const sinResultados = document.getElementById('sin-coincidencias');

        inputBuscar.addEventListener('input', function () {
            const termino = this.value.toLowerCase().trim();
            let visibles = 0;

            if (termino.length > 0) {
                btnLimpiar.classList.remove('d-none');
            } else {
                btnLimpiar.classList.add('d-none');
            }

            filas.forEach(fila => {
                const contenido = fila.textContent.toLowerCase();
                if (contenido.includes(termino)) {
                    fila.style.display = '';
                    visibles++;
                } else {
                    fila.style.display = 'none';
                }
            });

            if (visibles === 0 && filas.length > 0) {
                sinResultados.classList.remove('d-none');
            } else {
                sinResultados.classList.add('d-none');
            }
        });

        btnLimpiar.addEventListener('click', function () {
            inputBuscar.value = '';
            inputBuscar.dispatchEvent(new Event('input'));
            inputBuscar.focus();
        });
    });
</script>
@endsection