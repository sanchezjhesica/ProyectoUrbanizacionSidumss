@extends('layouts.operador')

@push('styles')
    @vite('resources/css/views/operador/dashboard.css')
@endpush

@section('content')
<div class="operador-stellar">
    <!-- 1. CABECERA -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 border-bottom pb-3 gap-3">
        <div>
            <h2 class="text-stellar-blue fw-bold mb-0"><i class="fas fa-tachometer-alt me-2"></i> Información de Trabajo</h2>
            <p class="text-muted mb-0">Bienvenido, <b>{{ Auth::user()->nombre }}</b>. Monitoreo y control de consumos de agua en tiempo real.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-blue-soft text-stellar-blue p-2 px-3 rounded-pill fw-bold">
                <i class="far fa-calendar-alt me-1"></i> {{ date('d/m/Y') }}
            </span>
            <a href="{{ route('operador.lecturas.crear') }}" class="btn btn-stellar-blue shadow-sm text-nowrap">
                <i class="fas fa-plus-circle me-1"></i> Nueva Medición
            </a>
        </div>
    </div>

    <!-- 2. TARJETAS DE ESTADÍSTICAS MEJORADAS -->
    <div class="row g-3 g-md-4 mb-4">
        
        <!-- CARD 1: LECTURAS HOY -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-glass border-start border-4 border-info">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted uppercase-tracking">Mediciones Hoy</span>
                        <div class="icon-circle-sm bg-info-soft text-info"><i class="fas fa-calendar-check"></i></div>
                    </div>
                    <h2 class="fw-bold text-dark mb-0">{{ $lecturasHoyCount }}</h2>
                    <small class="text-muted view-style-dbc8d1cddf" >Registradas en su turno</small>
                </div>
            </div>
        </div>

        <!-- CARD 2: COBERTURA / AVANCE DEL MES -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-glass border-start border-4 border-success">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted uppercase-tracking">Avance Mensual</span>
                        <div class="icon-circle-sm bg-success-soft text-success"><i class="fas fa-tasks"></i></div>
                    </div>
                    <h2 class="fw-bold text-dark mb-1">{{ $lecturasDelMesCount }} <small class="text-muted fs-6 fw-normal">/ {{ $totalCasas }}</small></h2>
                    <div class="progress progress-sm mb-1 view-style-119946eef6" >
                        <div class="progress-bar bg-success view-style-e178c365b5" role="progressbar"  style="--view-style-e178c365b5-value-0: {{ $porcentajeAvance }}%;"></div>
                    </div>
                    <small class="text-muted view-style-cb8a8ef79f" >{{ $porcentajeAvance }}% medido (Faltan: {{ $casasFaltantes }})</small>
                </div>
            </div>
        </div>

        <!-- CARD 3: VOLUMEN TOTAL MEDIDO (m³) -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-glass border-start border-4 border-primary">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted uppercase-tracking">Volumen Total</span>
                        <div class="icon-circle-sm bg-blue-soft text-stellar-blue"><i class="fas fa-faucet-drip"></i></div>
                    </div>
                    <h2 class="fw-bold text-dark mb-0">{{ number_format($consumoTotalMes, 1) }} <small class="fs-6 fw-normal">m³</small></h2>
                    <small class="text-muted view-style-dbc8d1cddf" >Agua consumida este mes</small>
                </div>
            </div>
        </div>

        <!-- CARD 4: PROMEDIO POR CASA (m³) -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-glass border-start border-4 border-warning">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted uppercase-tracking">Promedio Casa</span>
                        <div class="icon-circle-sm bg-warning-soft text-warning-dark"><i class="fas fa-chart-line"></i></div>
                    </div>
                    <h2 class="fw-bold text-dark mb-0">{{ number_format($promedioConsumo, 1) }} <small class="fs-6 fw-normal">m³</small></h2>
                    <small class="text-muted view-style-dbc8d1cddf" >Consumo medio por vivienda</small>
                </div>
            </div>
        </div>

    </div>

    <!-- 3. HISTORIAL RECIENTE CON DETALLE DE LECTURAS -->
    <div class="card border-0 shadow-sm rounded-4 card-glass overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-history me-2 text-stellar-blue"></i> Historial Reciente de Mediciones</h5>
            <span class="badge bg-light text-muted border">Últimas 10 lecturas</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-soft text-muted">
                        <tr>
                            <th class="ps-4 py-3 uppercase-tracking">Vivienda / Dueño</th>
                            <th class="py-3 uppercase-tracking text-center">Lectura Anterior</th>
                            <th class="py-3 uppercase-tracking text-center">Lectura Actual</th>
                            <th class="py-3 uppercase-tracking text-center">Consumo Neto</th>
                            <th class="py-3 text-end pe-4 uppercase-tracking">Fecha y Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lecturas as $l)
                        <tr>
                            <!-- VIVIENDA -->
                            <td class="ps-4">
                                <span class="badge bg-blue-soft text-stellar-blue font-monospace me-1">Casa #{{ $l->vivienda->nro_casa }}</span>
                                <span class="fw-bold text-dark small">{{ $l->vivienda->propietario->nombre ?? 'Sin Propietario' }} {{ $l->vivienda->propietario->apellido_paterno ?? '' }}</span>
                            </td>

                            <!-- ANTERIOR -->
                            <td class="text-center text-muted font-monospace view-style-933a8ef29e" >
                                {{ number_format($l->lectura_anterior, 2) }} m³
                            </td>

                            <!-- ACTUAL -->
                            <td class="text-center fw-bold text-dark font-monospace view-style-933a8ef29e" >
                                {{ number_format($l->lectura_actual, 2) }} m³
                            </td>

                            <!-- CONSUMO -->
                            <td class="text-center">
                                <span class="badge bg-info-soft text-info px-2.5 py-1 fw-bold font-monospace view-style-22b62ca48b" >
                                    +{{ number_format($l->consumo_m3, 2) }} m³
                                </span>
                            </td>

                            <!-- FECHA -->
                            <td class="text-end pe-4 small text-muted">
                                <i class="far fa-clock me-1 text-primary"></i>
                                {{ \Carbon\Carbon::parse($l->fecha_registro)->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-faucet-drip fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">No se han registrado mediciones todavía.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@endsection