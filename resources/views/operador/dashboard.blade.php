@extends('layouts.operador')

@section('content')
<div class="operador-stellar">
    <!-- 1. CABECERA -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 border-bottom pb-3 gap-3">
        <div>
            <h2 class="text-stellar-blue fw-bold mb-0"><i class="fas fa-tachometer-alt me-2"></i>Informacion de Trabajo</h2>
            <p class="text-muted mb-0">Bienvenido, <b>{{ Auth::user()->nombre }}</b>. Gestión de mediciones en tiempo real.</p>
        </div>
        <div class="text-md-end">
            <span class="badge bg-blue-soft text-stellar-blue p-2 px-3 rounded-pill fw-bold">
                <i class="far fa-calendar-alt me-1"></i> {{ date('d/m/Y') }}
            </span>
        </div>
    </div>

    <!-- 2. TARJETAS DE ESTADÍSTICAS RÁPIDAS -->
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-glass border-start border-5 border-info">
                <div class="card-body text-center">
                    <h6 class="text-muted small text-uppercase fw-bold letter-spacing-1">Lecturas Hoy</h6>
                    <h2 class="fw-bold text-dark mt-2">{{ count($lecturasHoy ?? []) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-glass border-start border-5 border-success">
                <div class="card-body text-center">
                    <h6 class="text-muted small text-uppercase fw-bold letter-spacing-1">Lecturas del Mes</h6>
                    <h2 class="fw-bold text-dark mt-2">{{ count($lecturas ?? []) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 d-none d-md-block">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-glass border-start border-5 border-primary">
                <div class="card-body text-center">
                    <h6 class="text-muted small text-uppercase fw-bold letter-spacing-1">Promedio Mensual</h6>
                    <h2 class="fw-bold text-dark mt-2">{{ number_format($lecturas->avg('consumo_m3') ?? 0, 1) }} <small class="fs-6 fw-normal">m³</small></h2>
                </div>
            </div>
        </div>
    </div>
        <!-- 4. ÚLTIMOS REGISTROS -->
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-glass overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-history me-2 text-stellar-blue"></i> Historial Reciente</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light-soft">
                                <tr>
                                    <th class="ps-4 py-3 uppercase-tracking">Casa</th>
                                    <th class="py-3 uppercase-tracking">Consumo</th>
                                    <th class="py-3 text-end pe-4 uppercase-tracking">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lecturas->take(5) as $l)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-dark">#{{ $l->vivienda->nro_casa }}</span>
                                    </td>
                                    <td>
                                        <span class="text-info fw-bold">{{ number_format($l->consumo_m3, 2) }} m³</span>
                                    </td>
                                    <td class="text-end pe-4 small text-muted">
                                        {{ \Carbon\Carbon::parse($l->fecha_registro)->format('d/m H:i') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">No hay registros hoy.</td>
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
        --stellar-grad: linear-gradient(45deg, #79f1a4 15%, #0e5cad 85%);
        --stellar-button: linear-gradient(45deg, #22349e 0%, #8183e6 100%);
    }

    /* GLASSMORPHISM */
    .card-glass {
        background: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }

    .text-stellar-blue { color: var(--stellar-blue); }
    .bg-blue-soft { background-color: rgba(14, 92, 173, 0.1); }
    .bg-light-soft { background-color: rgba(248, 249, 250, 0.5); }
    
    .uppercase-tracking { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1.2px; font-weight: 700; color: #888; }
    .letter-spacing-1 { letter-spacing: 1px; }

    /* Icono circular grande */
    .icon-circle-lg {
        width: 80px; height: 80px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }

    /* Botón Stellar Blue */
    .btn-stellar-blue {
        background: var(--stellar-button);
        color: white !important;
        border-radius: 50px;
        font-weight: 700;
        border: none;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.85rem;
        transition: 0.3s;
    }
    .btn-stellar-blue:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(34, 52, 158, 0.3); }

    .rounded-4 { border-radius: 1.25rem !important; }
</style>
@endsection