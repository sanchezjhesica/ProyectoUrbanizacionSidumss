@extends('layouts.propietario')

@push('styles')
    @vite('resources/css/views/propietario/avisos.css')
@endpush

@section('content')
<div class="propietario-stellar">
    <!-- 1. CABECERA DE SECCIÓN -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-white border-opacity-25 pb-4">
        <div>
            <h2 class="text-stellar-blue mb-0 fw-bold"><i class="fas fa-file-invoice-dollar me-2"></i> Mis Avisos de Cobro</h2>
            <p class="text-muted mb-0 small">Consulte, descargue y pague sus avisos mensuales de forma digital.</p>
        </div>
    </div>

    {{-- ALERTAS DE ÉXITO --}}
    @if(session('success'))
        <div class="alert alert-stellar-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- 2. NAVEGACIÓN POR PESTAÑAS (TABS RESPONSIVOS) -->
    <div class="tabs-container-scroll mb-4">
        <ul class="nav nav-pills flex-nowrap" id="pills-tab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active btn-stellar-tab me-2 shadow-sm" id="pills-agua-tab" data-bs-toggle="pill" data-bs-target="#pills-agua" type="button">
                    <i class="fas fa-tint"></i> <span class="ms-1">Agua Potable</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link btn-stellar-tab me-2 shadow-sm" id="pills-mantenimiento-tab" data-bs-toggle="pill" data-bs-target="#pills-mantenimiento" type="button">
                    <i class="fas fa-tools"></i> <span class="ms-1">Mantenimiento</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link btn-stellar-tab shadow-sm" id="pills-remesas-tab" data-bs-toggle="pill" data-bs-target="#pills-remesas" type="button">
                    <i class="fas fa-hand-holding-usd"></i> <span class="ms-1">Expensas</span>
                </button>
            </li>
        </ul>
    </div>

    <!-- 3. CONTENIDO DE LAS PESTAÑAS -->
    <div class="tab-content" id="pills-tabContent">
        
        <!-- SECCIÓN 1: AGUA -->
        <div class="tab-pane fade show active" id="pills-agua" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden card-glass">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light-soft text-muted">
                            <tr>
                                <th class="ps-4 py-3 uppercase-tracking">Periodo / Inmueble</th>
                                <th class="py-3 uppercase-tracking">Total</th>
                                <th class="py-3 text-center uppercase-tracking">Estado</th>
                                <th class="py-3 text-center uppercase-tracking">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($avisosAgua as $a)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark text-capitalize">
                                        {{ \Carbon\Carbon::create()->month($a->mes)->translatedFormat('F') }} {{ $a->anio }}
                                    </div>
                                    <!-- NÚMERO DE CASA -->
                                    <span class="badge bg-blue-soft text-stellar-blue font-monospace mt-1 view-style-dbc8d1cddf" >
                                        <i class="fas fa-home me-1"></i> Casa #{{ $a->vivienda->nro_casa ?? 'S/N' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-stellar-blue fs-6">Bs. {{ number_format($a->total_real ?? $a->total_pagar, 2) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-stellar {{ $a->estado_pago == 'Pagado' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                        {{ $a->estado_pago }}
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="d-flex justify-content-center gap-2">
                                        @if($a->estado_pago == 'Pagado')
                                            <a href="{{ route('propietario.descargar.agua', $a->id_cobro_agua) }}" class="btn btn-view-stellar" title="Descargar Recibo Oficial">
                                                <i class="fas fa-file-pdf me-1"></i> PDF
                                            </a>
                                        @else
                                            <button class="btn btn-qr-stellar" data-bs-toggle="modal" data-bs-target="#modalQR" data-id="{{ $a->id_cobro_agua }}" data-tipo="agua">
                                                <i class="fas fa-qrcode me-1"></i> Pagar QR
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-5 text-muted small">No hay deudas de agua registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2: MANTENIMIENTO -->
        <div class="tab-pane fade" id="pills-mantenimiento" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden card-glass">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-soft text-muted">
                        <tr>
                            <th class="ps-4 py-3 uppercase-tracking">Periodo / Inmueble</th>
                            <th class="py-3 uppercase-tracking">Monto</th>
                            <th class="py-3 text-center uppercase-tracking">Estado</th>
                            <th class="py-3 text-center uppercase-tracking">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($avisosMantenimiento as $m)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark text-capitalize">
                                    {{ \Carbon\Carbon::create()->month($m->mes)->translatedFormat('F') }} {{ $m->anio }}
                                </div>
                                <!-- NÚMERO DE CASA -->
                                <span class="badge bg-blue-soft text-stellar-blue font-monospace mt-1 view-style-dbc8d1cddf" >
                                    <i class="fas fa-home me-1"></i> Casa #{{ $m->vivienda->nro_casa ?? 'S/N' }}
                                </span>
                            </td>
                            <td><span class="fw-bold text-stellar-blue">Bs. {{ number_format($m->monto_fijo, 2) }}</span></td>
                            <td class="text-center">
                                <span class="badge-stellar {{ $m->estado_pago == 'Pagado' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                    {{ $m->estado_pago }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    @if($m->estado_pago == 'Pagado')
                                        <a href="{{ route('propietario.descargar.mantenimiento', $m->id_cobro_mantenimiento) }}" class="btn btn-view-stellar" title="Descargar Recibo Oficial">
                                            <i class="fas fa-file-pdf me-1"></i> PDF
                                        </a>
                                    @else
                                        <button class="btn btn-qr-stellar" data-bs-toggle="modal" data-bs-target="#modalQR" data-id="{{ $m->id_cobro_mantenimiento }}" data-tipo="mantenimiento">
                                            <i class="fas fa-qrcode me-1"></i> Pagar QR
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted">No hay cobros de mantenimiento pendientes.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECCIÓN 3: REMESAS -->
        <div class="tab-pane fade" id="pills-remesas" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden card-glass">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-soft text-muted">
                        <tr>
                            <th class="ps-4 py-3 uppercase-tracking">Periodo / Inmueble</th>
                            <th class="py-3 uppercase-tracking">Monto Total</th>
                            <th class="py-3 text-center uppercase-tracking">Estado</th>
                            <th class="py-3 text-center uppercase-tracking">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($avisosRemesas as $r)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">
                                    Planilla {{ \Carbon\Carbon::create()->month($r->mes)->translatedFormat('F') }} {{ $r->anio }}
                                </div>
                                <!-- NÚMERO DE CASA -->
                                <span class="badge bg-blue-soft text-stellar-blue font-monospace mt-1 view-style-dbc8d1cddf" >
                                    <i class="fas fa-home me-1"></i> Casa #{{ $r->vivienda->nro_casa ?? 'S/N' }}
                                </span>
                            </td>
                            <td><span class="fw-bold text-stellar-blue">Bs. {{ number_format($r->total_mes, 2) }}</span></td>
                            <td class="text-center">
                                <span class="badge-stellar {{ $r->estado_pago == 'Pagado' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                    {{ $r->estado_pago }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    @if($r->estado_pago == 'Pagado')
                                        <a href="{{ route('propietario.descargar.remesas', $r->id_referencia) }}" class="btn btn-view-stellar" title="Descargar Recibo Oficial">
                                            <i class="fas fa-file-pdf me-1"></i> PDF
                                        </a>
                                    @else
                                        <button class="btn btn-qr-stellar" data-bs-toggle="modal" data-bs-target="#modalQR" data-id="{{ $r->id_referencia }}" data-tipo="remesas">
                                            <i class="fas fa-qrcode me-1"></i> Pagar QR
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted">No hay remesas registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL QR -->
<div class="modal fade" id="modalQR" tabindex="-1" aria-labelledby="modalQRLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-stellar-blue" id="modalQRLabel">Pago con QR</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <p class="text-muted small">Escanee y realice la transferencia desde su banco.</p>
                    
                    @if($config && $config->qr_pago && $config->qr_pago !== 'sin_qr')
                        <!-- SI HAY QR VÁLIDO: Muestra la imagen -->
                        <img src="{{ asset('storage/' . $config->qr_pago) }}" class="img-fluid rounded-3 border shadow-sm" style="max-width: 180px; height: auto;" alt="QR Pago">
                    @else
                        <!-- SI NO HAY QR: Muestra el logo por defecto y la advertencia -->
                        <img src="{{ asset('img/logo.jpg') }}" class="img-fluid rounded-3 border shadow-sm opacity-50" style="max-width: 160px; height: auto;" alt="Logo por defecto">
                        <p class="text-danger small mt-2">QR no disponible</p>
                    @endif
                </div>

                <hr class="opacity-10">

                <form action="{{ route('propietario.comprobante.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_pago" id="input_id_pago">
                    <input type="hidden" name="tipo_pago" id="input_tipo_pago">

                    <div class="mb-4">
                        <label class="form-label-stellar">Adjuntar Comprobante (Imagen)</label>
                        <input type="file" name="comprobante" class="form-control form-stellar" accept="image/*" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light rounded-pill w-50 fw-bold" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-stellar-submit w-50 py-2 fw-bold">Enviar Pago</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalQR = document.getElementById('modalQR');
        if(modalQR) {
            document.body.appendChild(modalQR);
            modalQR.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                document.getElementById('input_id_pago').value = button.getAttribute('data-id');
                document.getElementById('input_tipo_pago').value = button.getAttribute('data-tipo');
            });
        }
    });
</script>
@endsection