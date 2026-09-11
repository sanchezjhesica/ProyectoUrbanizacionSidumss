@extends('layouts.admin')

@section('content')
<div class="gestion-cobros-stellar">
    <!-- 1. CABECERA RESPONSIVA -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 border-bottom pb-4 gap-3">
        <div>
            <h2 class="text-stellar-blue mb-0 fw-bold"><i class="fas fa-search-dollar me-2"></i> Gestión Integral de Cobros</h2>
            <p class="text-muted mb-0">Monitoreo de consumos y control de pagos mensuales por vivienda.</p>
        </div>
    </div>

    <!-- 2. MENSAJES DE ÉXITO/ERROR -->
    @if(session('success'))
        <div class="alert alert-stellar alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 3. BUSCADOR POR CASA (FILTRADO) -->
    <div class="card border-0 shadow-sm rounded-4 mb-5 bg-light-soft border-start border-4 border-stellar-blue">
        <div class="card-body p-4">
            <form action="{{ route('admin.lecturas.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-9">
                    <label class="form-label-stellar"><i class="fas fa-filter me-2"></i>Filtrar por Vivienda</label>
                    <select name="id_vivienda" class="form-select select2-stellar">
                        <option value="">-- Ver todos los recibos (Sin filtro) --</option>
                        @foreach($viviendas as $v)
                            <option value="{{ $v->id_vivienda }}" {{ request('id_vivienda') == $v->id_vivienda ? 'selected' : '' }}>
                                Casa #{{ $v->nro_casa }} - Propietario: {{ $v->propietario->nombre ?? 'Sin Asignar' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-stellar-blue w-100">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                        @if(request('id_vivienda'))
                            <a href="{{ route('admin.lecturas.index') }}" class="btn btn-light rounded-pill border shadow-sm" title="Limpiar filtro">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request('id_vivienda'))
        <div class="alert alert-info-stellar border-0 shadow-sm rounded-4 mb-4">
            <i class="fas fa-history me-2"></i> Historial completo de la <b>Casa #{{ $viviendas->where('id_vivienda', request('id_vivienda'))->first()->nro_casa }}</b>
        </div>
    @endif

    <!-- 4. NAVEGACIÓN POR PESTAÑAS (TABS) -->
    <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active btn-stellar-tab me-2 shadow-sm" data-bs-toggle="pill" data-bs-target="#pills-agua">
                <i class="fas fa-tint me-2"></i> 1. Agua Potable
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link btn-stellar-tab me-2 shadow-sm" data-bs-toggle="pill" data-bs-target="#pills-mante">
                <i class="fas fa-tools me-2"></i> 2. Mantenimiento
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link btn-stellar-tab shadow-sm" data-bs-toggle="pill" data-bs-target="#pills-remesas">
                <i class="fas fa-hand-holding-usd me-2"></i> 3. Expensas / Remesas
            </button>
        </li>
    </ul>

    <!-- 5. CONTENIDO DE LAS PESTAÑAS -->
    <div class="tab-content" id="pills-tabContent">
        
        <!-- PESTAÑA 1: AGUA -->
        <div class="tab-pane fade show active" id="pills-agua" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 uppercase-tracking">Vivienda</th>
                                <th class="uppercase-tracking">Periodo</th>
                                <th class="uppercase-tracking">Consumo</th>
                                <th class="uppercase-tracking">Total</th>
                                <th class="uppercase-tracking">Estado</th>
                                <th class="text-center uppercase-tracking">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cobrosAgua as $a)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-dark">Casa #{{ $a->vivienda->nro_casa }}</span><br>
                                    <small class="text-muted">{{ $a->vivienda->propietario->nombre ?? 'S/N' }}</small>
                                </td>
                                <td>{{ \Carbon\Carbon::create()->month($a->mes)->translatedFormat('F') }} {{ $a->anio }}</td>
                                <td><span class="badge-stellar bg-info-soft text-info">{{ $a->lectura->consumo_m3 ?? 0 }} m³</span></td>
                                
                                <!-- TOTAL CORREGIDO CON RESERVAS -->
                                <td class="fw-bold text-stellar-blue">
                                    Bs. {{ number_format($a->total_real ?? $a->total_pagar, 2) }}
                                    @if(($a->monto_reservas ?? 0) > 0)
                                        <br><small class="text-muted fw-normal" style="font-size: 0.72rem;">(Inc. Bs. {{ number_format($a->monto_reservas, 2) }} reservas)</small>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge-stellar {{ $a->estado_pago == 'Pagado' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                        {{ $a->estado_pago }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        @if($a->estado_pago == 'Pendiente')
                                            <form action="{{ route('admin.pagar.agua', $a->id_cobro_agua) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-sm btn-success rounded-pill px-3 shadow-sm fw-bold">Cobrar</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.descargar.agua', $a->id_cobro_agua) }}" class="btn-icon-pdf" title="Descargar Recibo PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-5 text-muted">No se encontraron registros de agua.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PESTAÑA 2: MANTENIMIENTO -->
        <div class="tab-pane fade" id="pills-mante" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 uppercase-tracking">Vivienda</th>
                                <th class="uppercase-tracking">Periodo</th>
                                <th class="uppercase-tracking">Monto Fijo</th>
                                <th class="uppercase-tracking">Estado</th>
                                <th class="text-center uppercase-tracking">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cobrosMante as $m)
                            <tr>
                                <td class="ps-4"><b>Casa #{{ $m->vivienda->nro_casa }}</b></td>
                                <td>{{ \Carbon\Carbon::create()->month($m->mes)->translatedFormat('F') }} {{ $m->anio }}</td>
                                <td>Bs. {{ number_format($m->monto_fijo, 2) }}</td>
                                <td>
                                    <span class="badge-stellar {{ $m->estado_pago == 'Pagado' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                        {{ $m->estado_pago }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        @if($m->estado_pago == 'Pendiente')
                                            <form action="{{ route('admin.pagar.mantenimiento', $m->id_cobro_mantenimiento) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-sm btn-success rounded-pill px-3 shadow-sm fw-bold">Cobrar</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.descargar.mantenimiento', $m->id_cobro_mantenimiento) }}" class="btn-icon-pdf" title="Descargar PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-5 text-muted">No hay cobros de mantenimiento registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PESTAÑA 3: REMESAS -->
        <div class="tab-pane fade" id="pills-remesas" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 uppercase-tracking">Concepto</th>
                                <th class="uppercase-tracking">Periodo</th>
                                <th class="uppercase-tracking">Monto Planilla</th>
                                <th class="uppercase-tracking">Estado</th>
                                <th class="text-center uppercase-tracking">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cobrosRemesas as $r)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">Planilla de Expensas</div>
                                    <small class="text-muted">Seguridad, Jardinería, etc.</small>
                                </td>
                                <td>{{ \Carbon\Carbon::create()->month($r->mes)->translatedFormat('F') }} {{ $r->anio }}</td>
                                <td class="fw-bold text-stellar-blue">Bs. {{ number_format($r->total_mes, 2) }}</td>
                                <td>
                                    <span class="badge-stellar {{ $r->estado_pago == 'Pagado' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                        {{ $r->estado_pago }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        @if($r->estado_pago == 'Pendiente')
                                            <form action="{{ route('admin.pagar.remesas') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id_vivienda" value="{{ $r->id_vivienda }}">
                                                <input type="hidden" name="mes" value="{{ $r->mes }}">
                                                <input type="hidden" name="anio" value="{{ $r->anio }}">
                                                <button class="btn btn-sm btn-success rounded-pill px-3 shadow-sm fw-bold">Cobrar</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.descargar.remesas', $r->id_referencia) }}" class="btn-icon-pdf" title="Descargar PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-5 text-muted">No hay remesas registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* VARIABLES STELLAR */
    :root {
        --stellar-blue: #0e5cad;
        --stellar-button: linear-gradient(45deg, #22349e 0%, #8183e6 100%);
    }

    .text-stellar-blue { color: var(--stellar-blue); }
    .bg-light-soft { background-color: #f8f9fa; }
    .bg-success-soft { background-color: rgba(40, 167, 69, 0.1); }
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
    .bg-info-soft { background-color: rgba(23, 162, 184, 0.1); }
    
    .form-label-stellar {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
        color: var(--stellar-blue);
        margin-bottom: 8px;
        display: block;
    }

    .uppercase-tracking { 
        font-size: 0.65rem; 
        text-transform: uppercase; 
        letter-spacing: 1.2px; 
        font-weight: 700; 
        color: #888; 
    }

    /* Badges */
    .badge-stellar {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }

    /* Estilo de Pestañas */
    .btn-stellar-tab { 
        background: white; 
        border: 1px solid #ddd; 
        color: #555; 
        border-radius: 12px; 
        padding: 10px 22px; 
        font-weight: 600;
        transition: 0.3s; 
    }
    .nav-pills .nav-link.active { 
        background: var(--stellar-blue) !important; 
        color: white !important; 
        box-shadow: 0 4px 10px rgba(14, 92, 173, 0.2); 
    }

    /* Botones de Acción */
    .btn-stellar-blue { 
        background: var(--stellar-button); 
        color: white !important; 
        border-radius: 50px; 
        border: none; 
        padding: 10px 20px; 
        font-weight: bold; 
        text-transform: uppercase;
        font-size: 0.8rem;
        transition: 0.3s; 
    }
    .btn-stellar-blue:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(34, 52, 158, 0.3); }

    /* Botón Icono PDF */
    .btn-icon-pdf {
        display: inline-flex; width: 35px; height: 35px; background: #fff5f5;
        color: #e74c3c; border-radius: 50%; align-items: center; justify-content: center;
        text-decoration: none; border: 1px solid #f8d7da; transition: 0.3s;
    }
    .btn-icon-pdf:hover { background: #e74c3c; color: white; transform: scale(1.1); }

    /* Alertas */
    .alert-stellar { background: #fff; border-left: 5px solid var(--stellar-blue); color: var(--stellar-blue); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .alert-info-stellar { background: #eef7ff; border-left: 5px solid #3498db; color: #0e5cad; }

    .rounded-4 { border-radius: 1rem !important; }
</style>
@endsection