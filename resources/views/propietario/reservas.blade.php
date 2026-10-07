@extends('layouts.propietario')

@section('content')
<div class="reservas-stellar">
    <!-- 1. CABECERA DE SECCIÓN -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 mb-md-4 border-bottom pb-3 gap-3">
        <div>
            <h2 class="text-stellar-blue mb-1 fw-bold fs-4 fs-md-3">
                <i class="fas fa-calendar-check me-2"></i> Reservar Áreas Recreativas
            </h2>
            <p class="text-muted small mb-0">Solicite el uso del Salón de Eventos o canchas deportivas.</p>
        </div>
        <div class="badge bg-blue-soft text-stellar-blue py-2 px-3 rounded-pill fw-semibold" style="font-size: 0.78rem;">
            <i class="fas fa-info-circle me-1"></i> Costo cargado al aviso de Agua tras aprobación
        </div>
    </div>

    <!-- 2. MENSAJES DE ÉXITO O CRUCE DE HORARIOS -->
    @if(session('success'))
        <div class="alert alert-stellar alert-dismissible fade show mb-3 border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->has('cruce') || $errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3 border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> 
            <strong>Horario Ocupado:</strong> {{ $errors->first('cruce') ?? $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 g-xl-4">
        <!-- 3. FORMULARIO DE RESERVA CON HORARIOS -->
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-stellar-blue fs-6"><i class="fas fa-plus-circle me-1"></i> Nueva Solicitud</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('propietario.reservas.store') }}" method="POST">
                        @csrf
                        
                        <!-- Selección de Área -->
                        <div class="mb-3">
                            <label class="form-label-stellar">Área Recreativa</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-map-marker-alt"></i></span>
                                <select name="id_area" id="select-area" class="form-select form-stellar border-start-0" required>
                                    <option value="" disabled selected>Seleccione un espacio...</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id_area }}" 
                                                data-nombre="{{ strtolower($area->nombre_area) }}"
                                                {{ old('id_area') == $area->id_area ? 'selected' : '' }}>
                                            {{ $area->nombre_area }} — Bs. {{ number_format($area->costo_reserva, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Fecha del Evento -->
                        <div class="mb-3">
                            <label class="form-label-stellar">Fecha del Evento</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-calendar-day"></i></span>
                                <input type="date" name="fecha" class="form-control form-stellar border-start-0" value="{{ old('fecha') }}" min="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <!-- CAMPOS: HORA INICIO Y HORA FIN (ESPECIAL PARA WALLY) -->
                        <div class="row g-2 mb-4" id="contenedor-horarios">
                            <div class="col-6">
                                <label class="form-label-stellar">Hora Inicio</label>
                                <input type="time" name="hora_inicio" id="hora_inicio" class="form-control form-stellar" value="{{ old('hora_inicio') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label-stellar">Hora Fin</label>
                                <input type="time" name="hora_fin" id="hora_fin" class="form-control form-stellar" value="{{ old('hora_fin') }}">
                            </div>
                            <small class="text-muted mt-1 d-block" id="texto-ayuda-horario" style="font-size: 0.72rem;">
                                <i class="fas fa-info-circle me-1"></i> Obligatorio para Wally / Polifuncional.
                            </small>
                        </div>

                        <button type="submit" class="btn btn-stellar-submit w-100 py-2.5 shadow-sm">
                            <i class="fas fa-paper-plane me-2"></i> Enviar Solicitud
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- 4. LISTADO DE MIS RESERVAS (100% RESPONSIVO) -->
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark fs-6"><i class="fas fa-history me-1 text-stellar-blue"></i> Mis Solicitudes y Reservas</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive m-0">
                        <table class="table table-hover align-middle mb-0 w-100">
                            <thead class="bg-light border-bottom">
                                <tr>
                                    <th class="ps-3 py-3 uppercase-tracking" style="min-width: 125px;">Fecha y Horario</th>
                                    <th class="py-3 uppercase-tracking">Espacio</th>
                                    <th class="py-3 text-center uppercase-tracking" style="width: 85px;">Costo</th>
                                    <th class="pe-3 py-3 text-center uppercase-tracking" style="width: 125px;">Estado y Pago</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reservas as $r)
                                <tr>
                                    <!-- 1. Fecha y Horario de la Reserva -->
                                    <td class="ps-3 text-nowrap">
                                        <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                            <i class="far fa-calendar-alt me-1 text-stellar-blue"></i>
                                            {{ \Carbon\Carbon::parse($r->fecha_reserva)->format('d/m/Y') }}
                                        </div>
                                        @if(!empty($r->hora_inicio))
                                            <div class="badge bg-light text-stellar-blue border fw-bold mt-1" style="font-size: 0.7rem;">
                                                <i class="far fa-clock me-1 text-info"></i>{{ substr($r->hora_inicio, 0, 5) }} a {{ substr($r->hora_fin, 0, 5) }}
                                            </div>
                                        @else
                                            <small class="text-muted d-block" style="font-size: 0.7rem;">Día completo</small>
                                        @endif
                                    </td>

                                    <!-- 2. Espacio -->
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle me-2 flex-shrink-0">
                                                <i class="fas fa-star text-stellar-blue" style="font-size: 0.75rem;"></i>
                                            </div>
                                            <span class="fw-semibold text-dark lh-sm" style="font-size: 0.85rem;">{{ $r->nombre_area }}</span>
                                        </div>
                                    </td>

                                    <!-- 3. Costo -->
                                    <td class="text-center text-nowrap">
                                        <span class="text-dark fw-bold" style="font-size: 0.85rem;">Bs. {{ number_format($r->costo_pactado, 2) }}</span>
                                    </td>
                                    
                                    <!-- 4. Estado de Solicitud y Estado de Pago Juntos -->
                                    <td class="text-center text-nowrap pe-3">
                                        <!-- Insignia de Solicitud (Administrador) -->
                                        @if($r->estado_reserva == 'Aceptado')
                                            <span class="badge-stellar bg-success-soft text-success">
                                                <i class="fas fa-check-circle me-1"></i> Aceptado
                                            </span>
                                        @elseif($r->estado_reserva == 'Denegado')
                                            <span class="badge-stellar bg-danger-soft text-danger">
                                                <i class="fas fa-times-circle me-1"></i> Denegado
                                            </span>
                                        @else
                                            <span class="badge-stellar bg-warning-soft text-warning-dark">
                                                <i class="fas fa-clock me-1"></i> Pendiente
                                            </span>
                                        @endif

                                        <!-- Insignia de Pago (Cobro de Agua) -->
                                        <div class="mt-1">
                                            <span class="badge {{ $r->estado_pago == 'Pagado' ? 'bg-light text-success border border-success' : 'bg-light text-muted border' }}" style="font-size: 0.65rem;">
                                                <i class="fas fa-circle me-1" style="font-size: 5px;"></i> Pago: {{ $r->estado_pago }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fas fa-calendar-times fa-2x mb-2 d-block opacity-25"></i>
                                        Usted no ha realizado solicitudes de reserva aún.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
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
    .bg-blue-soft { background-color: rgba(14, 92, 173, 0.08); }
    
    .bg-success-soft { background-color: rgba(40, 167, 69, 0.12); color: #28a745 !important; }
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.12); color: #dc3545 !important; }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.18); color: #b7791f !important; }

    .uppercase-tracking {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 700;
        color: #718096;
    }

    .form-label-stellar {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 700;
        color: #777;
        margin-bottom: 6px;
    }

    .form-stellar {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 9px 12px;
        font-size: 0.88rem;
        transition: 0.2s;
    }
    .form-stellar:focus {
        border-color: var(--stellar-blue);
        box-shadow: 0 0 0 0.2rem rgba(14, 92, 173, 0.1);
    }

    .btn-stellar-submit {
        background: var(--stellar-button);
        color: white !important;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        border: none;
        transition: 0.2s;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }
    .btn-stellar-submit:hover { opacity: 0.95; transform: translateY(-1px); }

    .alert-stellar { background: #e8f5e9; border-left: 4px solid #2e7d32; color: #2e7d32; }

    .badge-stellar {
        padding: 4px 8px;
        border-radius: 50px;
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }

    .icon-circle {
        width: 26px;
        height: 26px;
        background: rgba(14, 92, 173, 0.08);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .rounded-4 { border-radius: 1rem !important; }
    .table-responsive { border: none; scrollbar-width: thin; }
</style>

<!-- SCRIPT: Si eligen Wally, resalta la importancia de las horas -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectArea = document.getElementById('select-area');
        const inputInicio = document.getElementById('hora_inicio');
        const inputFin = document.getElementById('hora_fin');
        const textoAyuda = document.getElementById('texto-ayuda-horario');

        function verificarArea() {
            const opcion = selectArea.options[selectArea.selectedIndex];
            const nombre = opcion ? (opcion.getAttribute('data-nombre') || '') : '';

            if (nombre.includes('wally') || nombre.includes('cancha') || nombre.includes('polifuncional')) {
                // Para Wally: Horas obligatorias y limpias para que elijan su turno
                inputInicio.setAttribute('required', 'required');
                inputFin.setAttribute('required', 'required');
                textoAyuda.className = 'text-danger fw-bold mt-1 d-block';
                textoAyuda.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i> Obligatorio indicar horario para la cancha.';
            } else {
                // Para Salón: Rellenar automáticamente con todo el día (08:00 a 23:59)
                inputInicio.value = '08:00';
                inputFin.value = '23:59';
                inputInicio.removeAttribute('required');
                inputFin.removeAttribute('required');
                textoAyuda.className = 'text-success fw-bold mt-1 d-block';
                textoAyuda.innerHTML = '<i class="fas fa-check-circle me-1"></i> Asignado automáticamente: Todo el día (08:00 a 23:59).';
            }
        }

        selectArea.addEventListener('change', verificarArea);
        if(selectArea.value) {
            verificarArea();
        }
    });
</script>
</script>
@endsection