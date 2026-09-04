@extends('layouts.admin')

@section('content')
<div class="morosidad-stellar">
    <!-- Cabecera -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 mb-md-5 border-bottom pb-4 gap-3">
        <div>
            <h2 class="text-danger-stellar mb-0 fw-bold"><i class="fas fa-exclamation-triangle me-2"></i> Reporte de Morosidad</h2>
            <p class="text-muted mb-0">Lista de propietarios con pagos pendientes y deudas acumuladas.</p>
        </div>
        <div class="no-print w-100 w-md-auto">
            {{-- BOTÓN DESCARGAR --}}
            <a href="{{ route('admin.reportes.morosidad.descargar') }}" class="btn btn-stellar-danger px-4 shadow-sm w-100">
                <i class="fas fa-file-pdf me-2"></i> Descargar Lista PDF
            </a>
        </div>
    </div>

    <!-- Resumen de Deuda -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="stat-box shadow-sm d-flex align-items-center p-4 border-start border-5 border-danger">
                <div class="icon-circle-danger me-4"><i class="fas fa-money-bill-wave"></i></div>
                <div>
                    <h6 class="text-muted mb-1 text-uppercase small fw-bold letter-spacing-1">Deuda Total Acumulada</h6>
                    <h2 class="fw-bold mb-0 text-danger-stellar">Bs. {{ number_format($morosos->sum('total_deuda'), 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-box shadow-sm d-flex align-items-center p-4 border-start border-5 border-stellar-blue">
                <div class="icon-circle-stellar me-4"><i class="fas fa-user-clock"></i></div>
                <div>
                    <h6 class="text-muted mb-1 text-uppercase small fw-bold letter-spacing-1">Casas Deudoras</h6>
                    <h2 class="fw-bold mb-0 text-stellar-blue">{{ $morosos->count() }} Viviendas</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="ps-4 py-3 uppercase-tracking">Casa</th>
                            <th class="py-3 uppercase-tracking">Propietario</th>
                            <th class="py-3 uppercase-tracking">Pendientes</th>
                            <th class="py-3 uppercase-tracking">Total Deuda</th>
                            <th class="py-3 text-center uppercase-tracking">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($morosos as $m)
                        <tr>
                            <td class="ps-4 fw-bold">
                                <span class="badge bg-blue-soft text-stellar-blue px-3 py-2">Casa #{{ $m->nro_casa }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $m->propietario->nombre ?? 'S/N' }} {{ $m->propietario->apellido_paterno ?? '' }}</div>
                                <small class="text-muted">CI: {{ $m->propietario->ci ?? '---' }}</small>
                            </td>
                            <td><span class="text-dark">{{ $m->cantidad_avisos }} recibos</span></td>
                            <td class="text-danger-stellar fw-bold">Bs. {{ number_format($m->total_deuda, 2) }}</td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-danger-soft text-danger-stellar px-3 py-2 fw-bold" style="font-size: 10px;">
                                    PENDIENTE
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                <p class="text-muted mb-0">No hay deudas pendientes en el sistema.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    :root { --stellar-blue: #0e5cad; --stellar-danger: #e74c3c; }
    .text-danger-stellar { color: var(--stellar-danger); }
    .text-stellar-blue { color: var(--stellar-blue); }
    .bg-danger-soft { background: rgba(231, 76, 60, 0.1); }
    .bg-blue-soft { background-color: rgba(14, 92, 173, 0.1); }
    .letter-spacing-1 { letter-spacing: 1px; }
    .uppercase-tracking { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1.2px; font-weight: 700; color: #888; }
    
    .stat-box { background: #fff; border-radius: 15px; }
    .icon-circle-danger { width: 50px; height: 50px; background: rgba(231, 76, 60, 0.1); color: var(--stellar-danger); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .icon-circle-stellar { width: 50px; height: 50px; background: rgba(14, 92, 173, 0.1); color: var(--stellar-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }

    .btn-stellar-danger { background: var(--stellar-danger); color: white !important; border-radius: 50px; font-weight: 700; border: none; transition: 0.3s; text-transform: uppercase; font-size: 0.8rem; }
    .btn-stellar-danger:hover { background: #c0392b; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3); }
    .rounded-4 { border-radius: 1.25rem !important; }
</style>
@endsection