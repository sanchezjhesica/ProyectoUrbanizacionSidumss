@extends('layouts.admin')

@section('content')
<div class="reporte-vivienda-stellar">
    <!-- 1. CABECERA RESPONSIVA -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 border-bottom pb-3 gap-3">
        <div>
            <h2 class="text-stellar-blue mb-0 fw-bold"><i class="fas fa-file-invoice me-2"></i> Reporte por Vivienda</h2>
            <p class="text-muted mb-0 small">Historial consolidado de servicios, consumo medido y áreas recreativas.</p>
        </div>
        @if($viviendaSeleccionada)
            <a href="{{ route('admin.reportes.vivienda.descargar', $viviendaSeleccionada->id_vivienda) }}" class="btn btn-stellar-blue px-4 shadow-sm w-100 w-md-auto">
                <i class="fas fa-file-pdf me-2"></i> Descargar Historial PDF
            </a>
        @endif
    </div>

    <!-- MENSAJE DE ERROR SI NO EXISTE LA CASA -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- 2. ÚNICO BUSCADOR LIMPIO POR NÚMERO DE CASA -->
    <div class="card border-0 shadow-sm mb-4 rounded-4 bg-light-soft no-print border-start border-4 border-stellar-blue">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('admin.reportes.vivienda') }}" method="GET">
                <label class="form-label-stellar mb-2">
                    <i class="fas fa-home me-1 text-stellar-blue"></i> Ingrese el Número de Casa
                </label>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <div class="input-group search-input-group flex-grow-1">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                            <i class="fas fa-hashtag"></i>
                        </span>
                        <input type="text" 
                               name="nro_casa" 
                               id="input-casa" 
                               list="sugerencias-casas" 
                               class="form-control form-stellar border-start-0 ps-2" 
                               placeholder="Escriba el número (Ej: 3, 23, 45...)" 
                               value="{{ request('nro_casa') ?? ($viviendaSeleccionada->nro_casa ?? '') }}" 
                               autocomplete="off" 
                               required 
                               autofocus>
                        
                        <!-- Sugerencias automáticas que aparecen mientras tecleas sin estorbar -->
                        <datalist id="sugerencias-casas">
                            @foreach($viviendas as $v)
                                <option value="{{ $v->nro_casa }}">Casa #{{ $v->nro_casa }} — {{ $v->propietario->nombre ?? 'Sin Asignar' }}</option>
                            @endforeach
                        </datalist>
                    </div>

                    <button type="submit" class="btn btn-stellar-blue px-4 text-nowrap">
                        <i class="fas fa-search me-1"></i> Consultar
                    </button>

                    @if(request('nro_casa') || isset($viviendaSeleccionada))
                        <a href="{{ route('admin.reportes.vivienda') }}" class="btn btn-light border rounded-pill px-3 d-flex align-items-center justify-content-center" title="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($viviendaSeleccionada)
    <!-- 3. INFO DE LA PROPIEDAD SELECCIONADA -->
    <div class="propiedad-info-card shadow-sm mb-4 p-4 bg-white rounded-4 border-start border-stellar-blue border-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <small class="text-uppercase fw-bold text-muted letter-spacing-1">Información de Unidad</small>
                <h3 class="mb-1 fw-bold text-dark">Casa #{{ $viviendaSeleccionada->nro_casa }}</h3>
                <p class="text-muted mb-0">Responsable: <b class="text-stellar-blue">{{ $viviendaSeleccionada->propietario->nombre ?? 'Sin Asignar' }} {{ $viviendaSeleccionada->propietario->apellido_paterno ?? '' }}</b></p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <span class="badge bg-blue-soft text-stellar-blue p-2 px-3 fw-bold rounded-pill">
                    <i class="fas fa-tachometer-alt me-2"></i>Medidor: {{ $viviendaSeleccionada->nro_medidor }}
                </span>
            </div>
        </div>
    </div>

    <!-- 4. GRILLA DE REPORTES DETALLADOS -->
    <div class="row g-4">
        
        <!-- HISTORIAL DE AGUA -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom border-info border-opacity-25">
                    <h6 class="mb-0 fw-bold text-info"><i class="fas fa-tint me-2"></i> Consumos de Agua</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3 py-2 uppercase-tracking">Periodo</th>
                                    <th class="uppercase-tracking">Monto</th>
                                    <th class="text-center uppercase-tracking">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pagosAgua as $a)
                                <tr>
                                    <td class="ps-3">{{ $a->mes }}/{{ $a->anio }}</td>
                                    <td class="fw-bold">Bs. {{ number_format($a->total_pagar, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge-stellar {{ $a->estado_pago == 'Pagado' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                            {{ $a->estado_pago }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-4 text-muted small">Sin registros de agua.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- HISTORIAL DE MANTENIMIENTO -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom border-warning border-opacity-25">
                    <h6 class="mb-0 fw-bold text-warning"><i class="fas fa-tools me-2"></i> Mantenimiento Fijo</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3 py-2 uppercase-tracking">Periodo</th>
                                    <th class="uppercase-tracking">Monto</th>
                                    <th class="text-center uppercase-tracking">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pagosMante as $m)
                                <tr>
                                    <td class="ps-3">{{ $m->mes }}/{{ $m->anio }}</td>
                                    <td class="fw-bold">Bs. {{ number_format($m->monto_fijo, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge-stellar {{ $m->estado_pago == 'Pagado' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                            {{ $m->estado_pago }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-4 text-muted small">Sin registros de mantenimiento.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- HISTORIAL DE EXPENSAS / REMESAS -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-stellar-blue"><i class="fas fa-hand-holding-usd me-2"></i> Detalle de Expensas y Remesas</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3 py-2 uppercase-tracking">Concepto</th>
                                    <th class="uppercase-tracking">Periodo</th>
                                    <th class="uppercase-tracking">Monto</th>
                                    <th class="text-center uppercase-tracking">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pagosRemesas as $r)
                                <tr>
                                    <td class="ps-3"><b>{{ $r->configuracion->nombre_remesa ?? 'Expensa Extra' }}</b></td>
                                    <td>{{ $r->mes }}/{{ $r->anio }}</td>
                                    <td class="fw-bold">Bs. {{ number_format($r->total_remesa ?? $r->monto_pactado, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge-stellar {{ $r->estado_pago == 'Pagado' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                            {{ $r->estado_pago }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted small">Sin registros de remesas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ÚLTIMAS LECTURAS REGISTRADAS -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-top border-info border-5">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-line me-2"></i> Consumo Reciente (m³)</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tbody>
                            @forelse($historialLecturas->take(6) as $l)
                            <tr>
                                <td class="ps-3 py-2 text-muted">Mes {{ $l->periodo_mes }} / {{ $l->periodo_anio }}</td>
                                <td class="text-end fw-bold text-info pe-3">{{ number_format($l->consumo_m3, 2) }} m³</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center py-3 text-muted small">Sin lecturas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- HISTORIAL DE ÁREAS RECREATIVAS / RESERVAS -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom border-success border-opacity-25 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-success">
                        <i class="fas fa-calendar-check me-2"></i> Reservas de Áreas Recreativas
                    </h6>
                    <span class="badge bg-blue-soft text-stellar-blue px-3 py-1 rounded-pill small fw-bold">
                        Total: {{ count($pagosReservas ?? []) }} reservas
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3 py-2 uppercase-tracking">Fecha Evento</th>
                                    <th class="uppercase-tracking">Espacio Recreativo</th>
                                    <th class="uppercase-tracking">Costo</th>
                                    <th class="text-center uppercase-tracking">Solicitud</th>
                                    <th class="text-center uppercase-tracking">Estado Pago</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pagosReservas ?? [] as $res)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">
                                        {{ \Carbon\Carbon::parse($res->fecha_reserva)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                        <span class="fw-semibold text-dark">{{ $res->nombre_area }}</span>
                                    </td>
                                    <td class="fw-bold text-dark">
                                        Bs. {{ number_format($res->costo_pactado, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if($res->estado_reserva == 'Aceptado')
                                            <span class="badge-stellar bg-success-soft text-success">Aceptado</span>
                                        @elseif($res->estado_reserva == 'Denegado')
                                            <span class="badge-stellar bg-danger-soft text-danger">Denegado</span>
                                        @else
                                            <span class="badge-stellar bg-warning-soft text-warning-dark">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-stellar {{ $res->estado_pago == 'Pagado' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                            {{ $res->estado_pago }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted small">
                                        Esta vivienda no tiene registros de reservas.
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
    @endif
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
    .bg-light-soft { background-color: #f8f9fa; }
    
    .uppercase-tracking { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; color: #888; }
    .letter-spacing-1 { letter-spacing: 1px; }

    .badge-stellar {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .form-label-stellar { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; color: #555; margin-bottom: 6px; display: block; }
    .form-stellar { border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 14px; font-size: 0.92rem; }
    .form-stellar:focus { border-color: var(--stellar-blue); box-shadow: 0 0 0 0.2rem rgba(14, 92, 173, 0.1); }

    .btn-stellar-blue { 
        background: var(--stellar-button); 
        color: white !important; 
        border-radius: 50px; 
        font-weight: bold; 
        border: none; 
        padding: 10px 24px;
        transition: 0.3s; 
        text-transform: uppercase;
        font-size: 0.78rem;
    }
    .btn-stellar-blue:hover { opacity: 0.92; transform: translateY(-1px); }

    .rounded-4 { border-radius: 1.25rem !important; }

    @media print {
        .no-print { display: none !important; }
    }
</style>
@endsection