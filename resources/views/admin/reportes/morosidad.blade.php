@extends('layouts.admin')

@section('content')
<div class="morosidad-stellar">
    <!-- Cabecera -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-4">
        <div>
            <h2 class="text-danger-stellar mb-0 fw-bold"><i class="fas fa-calendar-times me-2"></i> Morosidad por Periodo</h2>
            <p class="text-muted mb-0">Listado detallado de cobros pendientes organizados cronológicamente.</p>
        </div>
        <a href="{{ route('admin.reportes.morosidad.descargar') }}" class="btn btn-stellar-danger px-4 shadow-sm">
            <i class="fas fa-file-pdf me-2"></i> Descargar Reporte
        </a>
    </div>

    <!-- Stats -->
    <div class="row mb-5 g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 border-start border-5 border-danger">
                <h6 class="text-muted small fw-bold text-uppercase">Deuda Total Pendiente</h6>
                <h2 class="fw-bold text-danger-stellar">Bs. {{ number_format($totalDeudaGlobal, 2) }}</h2>
            </div>
        </div>
    </div>

    <!-- Bucle de Años y Meses -->
    @foreach($morosidadPorMes as $anio => $meses)
        <h4 class="fw-bold text-dark mt-5 mb-3"><i class="far fa-calendar-alt me-2"></i> Gestión {{ $anio }}</h4>
        
        @foreach($meses as $numMes => $cobros)
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="mb-0 fw-bold text-stellar-blue text-uppercase">
                        {{ \Carbon\Carbon::create()->month($numMes)->translatedFormat('F') }}
                    </h6>
                    <span class="badge bg-danger-soft text-danger-stellar rounded-pill">
                        Deuda Mes: Bs. {{ number_format($cobros->sum('monto'), 2) }}
                    </span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light small text-muted">
                            <tr>
                                <th class="ps-4">Casa</th>
                                <th>Propietario</th>
                                <th>Concepto</th>
                                <th class="text-end pe-4">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cobros as $c)
                            <tr>
                                <td class="ps-4 fw-bold">Casa #{{ $c->casa }}</td>
                                <td>{{ $c->propietario }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $c->concepto }}</span></td>
                                <td class="text-end pe-4 fw-bold text-danger-stellar">Bs. {{ number_format($c->monto, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @endforeach
</div>

<style>
    :root { --stellar-blue: #0e5cad; --stellar-danger: #e74c3c; }
    .text-danger-stellar { color: var(--stellar-danger); }
    .text-stellar-blue { color: var(--stellar-blue); }
    .bg-danger-soft { background: rgba(231, 76, 60, 0.1); }
    .btn-stellar-danger { background: var(--stellar-danger); color: white !important; border-radius: 50px; font-weight: 700; border: none; transition: 0.3s; }
    .rounded-4 { border-radius: 1.25rem !important; }
</style>
@endsection