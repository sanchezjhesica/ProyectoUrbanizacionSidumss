@extends('layouts.admin')

@push('styles')
    @vite('resources/css/views/admin/reportes/morosidad.css')
@endpush

@section('content')
<div class="morosidad-stellar">
    <!-- 1. CABECERA RESPONSIVA -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 border-bottom pb-4 gap-3">
        <div>
            <h2 class="text-danger-stellar mb-0 fw-bold"><i class="fas fa-calendar-times me-2"></i> Morosidad</h2>
            <p class="text-muted mb-0 small">Deudas organizadas por gestión y mes.</p>
        </div>
        <a href="{{ route('admin.reportes.morosidad.descargar') }}" class="btn btn-stellar-danger px-4 shadow-sm w-100 w-md-auto">
            <i class="fas fa-file-pdf me-2"></i> <span class="d-inline">Descargar PDF</span>
        </a>
    </div>

    <!-- 2. RESUMEN DE DEUDA -->
    <div class="row mb-4 g-3">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 border-start border-5 border-danger">
                <h6 class="text-muted small fw-bold text-uppercase letter-spacing-1">Deuda Total Global</h6>
                <h2 class="fw-bold text-danger-stellar mb-0">Bs. {{ number_format($totalDeudaGlobal, 2) }}</h2>
            </div>
        </div>
    </div>

    <!-- 3. LISTADO CRONOLÓGICO -->
    @foreach($morosidadPorMes as $anio => $meses)
        <div class="gestion-divider mt-5 mb-3">
            <span class="bg-dark text-white px-3 py-1 rounded-pill small fw-bold">GESTIÓN {{ $anio }}</span>
        </div>
        
        @foreach($meses as $numMes => $cobros)
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <!-- Header del Mes Responsivo -->
                <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center border-bottom gap-2">
                    <h6 class="mb-0 fw-bold text-stellar-blue text-uppercase">
                        <i class="fas fa-calendar-day me-2"></i>{{ \Carbon\Carbon::create()->month($numMes)->translatedFormat('F') }}
                    </h6>
                    <span class="badge bg-danger-soft text-danger-stellar rounded-pill px-3 py-2">
                        Total Mes: Bs. {{ number_format($cobros->sum('monto'), 2) }}
                    </span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light text-muted view-style-cb8a8ef79f" >
                                <tr>
                                    <th class="ps-4">CASA / DUEÑO</th>
                                    <th class="d-none d-md-table-cell">CONCEPTO</th>
                                    <th class="text-end pe-4">MONTO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cobros as $c)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="badge bg-blue-soft text-stellar-blue me-2">#{{ $c->casa }}</div>
                                            <div>
                                                <span class="fw-bold d-block text-dark small">{{ $c->propietario }}</span>
                                                <!-- Concepto visible solo en móvil (debajo del nombre) -->
                                                <small class="text-muted d-md-none">{{ $c->concepto }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Concepto oculto en móvil para ahorrar espacio -->
                                    <td class="d-none d-md-table-cell">
                                        <span class="badge bg-light text-dark border fw-normal">{{ $c->concepto }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <span class="fw-bold text-danger-stellar">Bs. {{ number_format($c->monto, 2) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach
</div>


@endsection