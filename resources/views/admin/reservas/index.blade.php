@extends('layouts.admin')

@section('content')
<div class="reservas-stellar">
    <!-- 1. ENCABEZADO CON BOTÓN DE DESCARGA ANUAL -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 mb-md-4 border-bottom pb-3 gap-3">
        <div>
            <h2 class="text-stellar-blue mb-1 fw-bold fs-4 fs-md-3">
                <i class="fas fa-calendar-check me-2"></i> Solicitudes de Reservas
            </h2>
            <p class="text-muted small mb-0">Gestione peticiones de áreas comunes y consulte el calendario mensual.</p>
        </div>
        
        <!-- BOTÓN DESCARGAR REPORTE ANUAL PDF -->
        <a href="{{ route('admin.reservas.descargar.anual', ['anio' => $anio ?? date('Y')]) }}" class="btn btn-stellar-blue px-3 py-2 shadow-sm text-nowrap">
            <i class="fas fa-file-pdf me-1"></i> Descargar Reporte Anual ({{ $anio ?? date('Y') }})
        </a>
    </div>

    <!-- 2. BARRA DE FILTROS (POR MES Y BUSCADOR) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light-soft no-print border-start border-4 border-stellar-blue">
        <div class="card-body p-3">
            <form action="{{ route('admin.reservas.index') }}" method="GET" class="row g-2 align-items-center">
                <!-- Filtro por Mes -->
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-calendar-alt"></i></span>
                        <select name="mes" class="form-select border-start-0" onchange="this.form.submit()">
                            <option value="">-- Ver todos los meses --</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ request('mes') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Buscador de texto -->
                <div class="col-12 col-md-6">
                    <div class="input-group search-box-stellar">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" id="input-buscar" class="form-control border-start-0 ps-0" placeholder="Buscar por residente, casa o área..." autocomplete="off">
                    </div>
                </div>

                @if(request('mes'))
                <div class="col-12 col-md-2">
                    <a href="{{ route('admin.reservas.index') }}" class="btn btn-light border w-100 rounded-pill">
                        <i class="fas fa-times me-1"></i> Quitar filtro
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- ALERTAS -->
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

    <!-- 3. TABLA DE RESERVAS CON FECHA Y HORARIO -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive m-0">
                <table class="table table-hover align-middle mb-0 w-100" id="tabla-reservas">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="ps-3 ps-md-4 py-3 uppercase-tracking" style="min-width: 140px;">Fecha y Horario</th>
                            <th class="py-3 uppercase-tracking">Residente / Casa</th>
                            <th class="py-3 uppercase-tracking">Área Común</th>
                            <th class="py-3 uppercase-tracking text-center">Costo</th>
                            <th class="py-3 uppercase-tracking text-center">Estado</th>
                            <th class="py-3 pe-3 pe-md-4 text-end uppercase-tracking" style="min-width: 130px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-reservas">
                        @forelse($reservas as $r)
                        <tr class="fila-reserva">
                            <!-- FECHA Y HORA DETALLADA -->
                            <td class="ps-3 ps-md-4 text-nowrap">
                                <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                    <i class="far fa-calendar-alt me-1 text-primary"></i>
                                    {{ date('d/m/Y', strtotime($r->fecha_reserva)) }}
                                </div>
                                @if(!empty($r->hora_inicio) && $r->hora_inicio != '08:00:00')
                                    <div class="badge bg-light text-stellar-blue border fw-bold mt-1" style="font-size: 0.72rem;">
                                        <i class="far fa-clock me-1 text-info"></i>
                                        {{ substr($r->hora_inicio, 0, 5) }} a {{ substr($r->hora_fin, 0, 5) }}
                                    </div>
                                @else
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">
                                        <i class="far fa-clock me-1"></i> Día completo
                                    </small>
                                @endif
                            </td>

                            <!-- RESIDENTE Y CASA -->
                            <td>
                                <div class="fw-bold text-dark text-nowrap">
                                    {{ $r->nombre }} {{ $r->apellido_paterno }}
                                    @if(!empty($r->nro_casa))
                                        <span class="badge bg-blue-soft text-stellar-blue font-monospace ms-1">Casa #{{ $r->nro_casa }}</span>
                                    @endif
                                </div>
                                <div class="small text-muted font-monospace">
                                    CI: {{ $r->ci }} @if($r->telefono) | Cel: {{ $r->telefono }} @endif
                                </div>
                            </td>

                            <!-- ÁREA COMÚN -->
                            <td>
                                <span class="fw-semibold text-dark text-nowrap">
                                    <i class="fas fa-map-marker-alt me-1 text-danger opacity-75"></i> {{ $r->nombre_area }}
                                </span>
                            </td>

                            <!-- COSTO -->
                            <td class="text-center fw-bold text-dark text-nowrap">
                                Bs. {{ number_format($r->costo_pactado, 2) }}
                            </td>

                            <!-- ESTADO DE LA RESERVA -->
                            <td class="text-center text-nowrap">
                                @if($r->estado_reserva == 'Aceptado')
                                    <span class="badge bg-success-soft text-success px-3 py-1.5 rounded-pill fw-bold">
                                        <i class="fas fa-check-circle me-1"></i> Aceptado
                                    </span>
                                @elseif($r->estado_reserva == 'Denegado')
                                    <span class="badge bg-danger-soft text-danger px-3 py-1.5 rounded-pill fw-bold">
                                        <i class="fas fa-times-circle me-1"></i> Denegado
                                    </span>
                                @else
                                    <span class="badge bg-warning-soft text-warning-dark px-3 py-1.5 rounded-pill fw-bold">
                                        <i class="fas fa-clock me-1"></i> Pendiente
                                    </span>
                                @endif
                            </td>

                            <!-- ACCIONES: ACEPTAR / DENEGAR DIRECTO -->
                            <td class="pe-3 pe-md-4 text-end text-nowrap">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <!-- ACEPTAR -->
                                    <form action="{{ route('admin.reservas.aceptar', $r->id_reserva) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-action-reserva btn-accept" title="Aceptar Solicitud" {{ $r->estado_reserva == 'Aceptado' ? 'disabled' : '' }}>
                                            <i class="fas fa-check"></i> <span class="d-none d-xl-inline ms-1">Aceptar</span>
                                        </button>
                                    </form>

                                    <!-- DENEGAR -->
                                    <form action="{{ route('admin.reservas.denegar', $r->id_reserva) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-action-reserva btn-reject" title="Denegar Solicitud" {{ $r->estado_reserva == 'Denegado' ? 'disabled' : '' }}>
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
                                No hay solicitudes de reserva para el periodo seleccionado.
                            </td>
                        </tr>
                        @endforelse

                        <tr id="sin-coincidencias" class="d-none">
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-search fa-2x mb-3 d-block text-secondary opacity-50"></i>
                                No se encontraron solicitudes que coincidan con la búsqueda.
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
    .bg-blue-soft { background-color: rgba(14, 92, 173, 0.08); }
    .bg-light-soft { background-color: #f8fafc; }
    .alert-stellar { background-color: #e8f5e9; color: #2e7d32; border-left: 5px solid #2e7d32; }

    .uppercase-tracking {
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 700;
        color: #718096;
    }

    .bg-success-soft { background-color: rgba(46, 125, 50, 0.12); color: #2e7d32 !important; }
    .bg-danger-soft { background-color: rgba(229, 62, 62, 0.12); color: #dc2626 !important; }
    .bg-warning-soft { background-color: rgba(243, 156, 18, 0.15); color: #b45309 !important; }

    /* Botones de acción */
    .btn-action-reserva {
        height: 32px;
        padding: 0 10px;
        border-radius: 8px;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: all 0.2s;
    }
    .btn-accept { background-color: #e8f5e9; border: 1px solid #c8e6c9; color: #2e7d32; }
    .btn-accept:hover:not(:disabled) { background-color: #2e7d32; color: white; }
    .btn-reject { background-color: #ffebee; border: 1px solid #ffcdd2; color: #c62828; }
    .btn-reject:hover:not(:disabled) { background-color: #c62828; color: white; }
    .btn-action-reserva:disabled { opacity: 0.35; cursor: not-allowed; }

    .btn-stellar-blue {
        background: var(--stellar-button);
        color: white !important;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.82rem;
        border: none;
        transition: 0.2s;
    }
    .btn-stellar-blue:hover { opacity: 0.92; transform: translateY(-1px); }

    .rounded-4 { border-radius: 1rem !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputBuscar = document.getElementById('input-buscar');
        const filas = document.querySelectorAll('.fila-reserva');
        const sinResultados = document.getElementById('sin-coincidencias');

        inputBuscar.addEventListener('input', function () {
            const termino = this.value.toLowerCase().trim();
            let visibles = 0;

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
    });
</script>
@endsection