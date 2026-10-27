@extends('layouts.admin')

@push('styles')
    @vite('resources/css/views/admin/reportes/index.css')
@endpush

@section('content')
<div class="reporte-stellar">
    <!-- Cabecera Responsiva -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 mb-md-5 border-bottom pb-4 gap-3">
        <div>
            <h2 class="text-stellar-blue mb-0 fw-bold"><i class="fas fa-chart-pie me-2"></i> Reporte Económico General</h2>
            <p class="text-muted mb-0">Balance consolidado de ingresos y egresos de la urbanización.</p>
        </div>
        <div class="no-print w-100 w-md-auto">
            {{-- BOTÓN ACTUALIZADO PARA DESCARGAR PDF --}}
            <a href="{{ route('admin.reportes.general.descargar') }}" class="btn btn-stellar-blue px-4 shadow-sm w-100">
                <i class="fas fa-file-pdf me-2"></i> Descargar Reporte PDF
            </a>
        </div>
    </div>

    <!-- 1. BLOQUE DE ESTADÍSTICAS (TOTALE) -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 border-bottom border-success border-5 h-100">
                <div class="card-body text-center p-4">
                    <h6 class="text-success fw-bold small text-uppercase letter-spacing-1">Total Ingresos</h6>
                    <h2 class="fw-bold text-dark mt-2">Bs. {{ number_format($totalIngresos, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 border-bottom border-danger border-5 h-100">
                <div class="card-body text-center p-4">
                    <h6 class="text-danger fw-bold small text-uppercase letter-spacing-1">Total Egresos</h6>
                    <h2 class="fw-bold text-dark mt-2">Bs. {{ number_format($totalEgresos, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 border-bottom border-stellar-blue border-5 h-100">
                <div class="card-body text-center p-4">
                    <h6 class="text-stellar-blue fw-bold small text-uppercase letter-spacing-1">Saldo en Caja</h6>
                    <h2 class="fw-bold text-dark mt-2">Bs. {{ number_format($saldoCaja, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. TABLA DE INGRESOS (VERDE) -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-header bg-success text-white py-3 border-0">
            <h5 class="mb-0 fw-bold"><i class="fas fa-arrow-circle-down me-2"></i> Últimos Ingresos Registrados</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 uppercase-tracking">Fecha / Casa</th>
                            <th class="py-3 uppercase-tracking">Concepto</th>
                            <th class="py-3 text-end pe-4 uppercase-tracking">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($listaIngresos as $ing)
                        <tr>
                            <td class="ps-4">
                                <small class="text-muted d-block">{{ \Carbon\Carbon::parse($ing->fecha)->format('d/m/y') }}</small>
                                <span class="fw-bold text-dark">Casa #{{ $ing->casa }}</span>
                            </td>
                            <td>{{ $ing->concepto }}</td>
                            <td class="text-end pe-4">
                                <span class="text-success fw-bold">Bs. {{ number_format($ing->monto, 2) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-5 text-muted">No hay ingresos registrados en el sistema.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 3. TABLA DE EGRESOS (ROJA) -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-danger text-white py-3 border-0">
            <h5 class="mb-0 fw-bold"><i class="fas fa-arrow-circle-up me-2"></i> Detalle de Egresos (Gastos)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 uppercase-tracking">Fecha</th>
                            <th class="py-3 uppercase-tracking">Descripción / Categoría</th>
                            <th class="py-3 text-end pe-4 uppercase-tracking">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($listaEgresos as $eg)
                        <tr>
                            <td class="ps-4">{{ \Carbon\Carbon::parse($eg->fecha_egreso)->format('d/m/y') }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $eg->descripcion }}</div>
                                <span class="badge bg-light text-muted border fw-normal">{{ $eg->categoria }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <span class="text-danger fw-bold">Bs. {{ number_format($eg->monto, 2) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-5 text-muted">No hay egresos registrados en el sistema.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@endsection