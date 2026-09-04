@extends('layouts.admin')

@section('content')
<div class="tarifas-stellar">
    <!-- 1. CABECERA RESPONSIVA -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 border-bottom pb-4 gap-3">
        <div>
            <h2 class="text-stellar-blue mb-0 fw-bold"><i class="fas fa-coins me-2"></i> Tarifas</h2>
            <p class="text-muted mb-0 small">Gestione los precios base de la urbanización.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-stellar-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 2. NAVEGACIÓN POR PESTAÑAS (Scrollable en móvil) -->
    <div class="tabs-container-scroll">
        <ul class="nav nav-pills mb-4 flex-nowrap flex-md-wrap" id="tarifasTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active btn-stellar-tab me-2 shadow-sm" data-bs-toggle="pill" data-bs-target="#global">
                    <i class="fas fa-tint"></i> <span class="d-none d-sm-inline ms-1">Agua/Mante</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link btn-stellar-tab me-2 shadow-sm" data-bs-toggle="pill" data-bs-target="#remesas">
                    <i class="fas fa-shield-alt"></i> <span class="d-none d-sm-inline ms-1">Remesas</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link btn-stellar-tab shadow-sm" data-bs-toggle="pill" data-bs-target="#areas">
                    <i class="fas fa-volleyball-ball"></i> <span class="d-none d-sm-inline ms-1">Áreas</span>
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content border-0">
        <!-- SECCIÓN 1: AGUA Y MANTENIMIENTO -->
        <div class="tab-pane fade show active" id="global" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 p-md-5">
                    <form action="{{ route('admin.tarifas.updateGlobal') }}" method="POST">
                        @csrf
                        <div class="row g-3 g-md-4">
                            <div class="col-sm-6 col-lg-3">
                                <label class="form-label-stellar">Precio m³ Agua</label>
                                <input type="number" step="0.01" name="precio_m3_agua" class="form-control form-stellar" value="{{ $tarifa->precio_m3_agua }}">
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <label class="form-label-stellar">Mantenimiento Fijo</label>
                                <input type="number" step="0.01" name="monto_mantenimiento_fijo" class="form-control form-stellar" value="{{ $tarifa->monto_mantenimiento_fijo }}">
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <label class="form-label-stellar">Alcantarillado</label>
                                <input type="number" step="0.01" name="monto_alcantarillado" class="form-control form-stellar" value="{{ $tarifa->monto_alcantarillado }}">
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <label class="form-label-stellar">Porcentaje Mora</label>
                                <input type="number" step="0.01" name="porcentaje_mora" class="form-control form-stellar" value="{{ $tarifa->porcentaje_mora }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-stellar-blue w-100 mt-4 py-3 fw-bold">ACTUALIZAR GLOBALES</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2: REMESAS -->
        <div class="tab-pane fade" id="remesas" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 uppercase-tracking">Concepto</th>
                                    <th class="py-3 uppercase-tracking">Monto Actual</th>
                                    <th class="py-3 text-center uppercase-tracking">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($remesas as $r)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark small text-uppercase">{{ $r->nombre_remesa }}</div>
                                    </td>
                                    <form action="{{ route('admin.tarifas.updateRemesa', $r->id_remesa_config) }}" method="POST">
                                        @csrf @method('PUT')
                                        <td>
                                            <div class="input-group input-group-sm" style="min-width: 110px;">
                                                <span class="input-group-text bg-light border-end-0">Bs.</span>
                                                <input type="number" step="0.01" name="monto_estandar" class="form-control border-start-0 fw-bold" value="{{ $r->monto_estandar }}">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="submit" class="btn btn-sm btn-outline-stellar rounded-pill px-3">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 3: ÁREAS RECREATIVAS -->
        <div class="tab-pane fade" id="areas" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 uppercase-tracking">Área</th>
                                    <th class="py-3 uppercase-tracking">Precio</th>
                                    <th class="py-3 text-center uppercase-tracking">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($areas as $a)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-dark small text-uppercase">{{ $a->nombre_area }}</span>
                                    </td>
                                    <form action="{{ route('admin.tarifas.updateArea', $a->id_area) }}" method="POST">
                                        @csrf @method('PUT')
                                        <td>
                                            <div class="input-group input-group-sm" style="min-width: 110px;">
                                                <span class="input-group-text bg-light border-end-0">Bs.</span>
                                                <input type="number" step="0.01" name="costo_reserva" class="form-control border-start-0 fw-bold" value="{{ $a->costo_reserva }}">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="submit" class="btn btn-sm btn-outline-stellar rounded-pill px-3">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                                @endforeach
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

    /* Tabs scrollable en móvil */
    .tabs-container-scroll {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }
    .tabs-container-scroll::-webkit-scrollbar { display: none; }

    .text-stellar-blue { color: var(--stellar-blue); }
    .uppercase-tracking { font-size: 0.6rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; color: #888; }

    /* Botones de pestañas */
    .btn-stellar-tab {
        background-color: white; border: 1px solid #eee; color: #666;
        border-radius: 50px; padding: 8px 18px; font-weight: 700; font-size: 0.8rem;
        transition: 0.3s; white-space: nowrap;
    }
    .nav-pills .nav-link.active { background-color: var(--stellar-blue) !important; color: white !important; }

    /* Inputs */
    .form-label-stellar { font-size: 0.65rem; text-transform: uppercase; font-weight: 800; color: #777; margin-bottom: 5px; display: block; }
    .form-stellar { border: 1px solid #e0e0e0; border-radius: 8px; padding: 8px 12px; font-size: 0.9rem; }
    .form-stellar:focus { border-color: var(--stellar-blue); box-shadow: 0 0 0 0.25rem rgba(14, 92, 173, 0.1); }

    /* Botones principales */
    .btn-stellar-blue {
        background: var(--stellar-button); color: white !important;
        border-radius: 50px; font-weight: 700; border: none;
        text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; transition: 0.3s;
    }

    .btn-outline-stellar { border: 1px solid var(--stellar-blue); color: var(--stellar-blue); font-weight: 600; }
    .btn-outline-stellar:hover { background: var(--stellar-blue); color: white; }

    .alert-stellar-success { background-color: #f0fff4; border-left: 5px solid #38a169; color: #276749; border-radius: 10px; }

    .rounded-4 { border-radius: 1.25rem !important; }

    /* Ajustes específicos para móviles extra */
    @media (max-width: 768px) {
        .card-body { padding: 1.2rem !important; }
        .table td { padding: 10px 5px !important; }
        .input-group-text { padding: 0.3rem 0.5rem; font-size: 0.75rem; }
    }
</style>
@endsection