@extends('layouts.admin')

@push('styles')
    @vite('resources/css/views/admin/imagenes/QR.css')
@endpush

@section('content')
<div class="imagenes-gestion-stellar">
    <!-- 1. ENCABEZADO -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-4">
        <div>
            <h2 class="text-stellar-blue mb-0 fw-bold"><i class="fas fa-qrcode me-2"></i> Gestión de Pagos por QR</h2>
            <p class="text-muted mb-0">Suba el código QR oficial y valide los comprobantes de los propietarios.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
            <i class="fas fa-times-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        <!-- 2. PANEL: SUBIR/ACTUALIZAR QR OFICIAL -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">QR Oficial de Cobro</h5>
                </div>
                <div class="card-body text-center p-4">
                    <div class="qr-preview-container mb-4 mx-auto">
                        @if(isset($config->qr_pago) && $config->qr_pago)
                            <img src="{{ asset('storage/' . $config->qr_pago) }}" class="img-fluid rounded-3 shadow-sm border" id="preview-qr">
                        @else
                            <div class="placeholder-qr rounded-3 border d-flex flex-column align-items-center justify-content-center text-muted">
                                <i class="fas fa-image fa-3x mb-2"></i>
                                <small>Sin QR cargado</small>
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('admin.tarifas.updateQR') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3 text-start">
                            <label class="form-label small fw-bold text-muted">SELECCIONAR NUEVO QR</label>
                            <input type="file" name="qr_pago" class="form-control form-stellar" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-stellar-blue w-100 py-2 fw-bold shadow-sm">
                            <i class="fas fa-upload me-2"></i> ACTUALIZAR QR
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- 3. PANEL: IMÁGENES RECIBIDAS (GALERÍA) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">Comprobantes Recibidos</h5>
                    <span class="badge bg-blue-soft text-stellar-blue px-3 py-1 rounded-pill small fw-bold">Total: {{ count($comprobantes) }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="uppercase-tracking">Propietario</th>
                                    <th class="uppercase-tracking">Concepto</th>
                                    <th class="uppercase-tracking text-center">Comprobante</th>
                                    <th class="uppercase-tracking text-center view-style-0ff0f36a38" >Acción / Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comprobantes as $c)
                                <tr>
                                    <!-- PROPIETARIO -->
                                    <td>
                                        <span class="fw-bold d-block text-dark">{{ $c->nombre }} {{ $c->apellido_paterno }}</span>
                                        <small class="text-muted font-monospace">CI: {{ $c->ci }}</small>
                                    </td>

                                    <!-- CONCEPTO DE PAGO -->
                                    <td>
                                        @if($c->tipo_pago == 'agua')
                                            <span class="badge bg-info-soft text-info rounded-pill px-2.5 py-1 fw-bold">
                                                <i class="fas fa-tint me-1"></i> AGUA #{{ $c->id_referencia_pago }}
                                            </span>
                                        @elseif($c->tipo_pago == 'mantenimiento')
                                            <span class="badge bg-warning-soft text-warning-dark rounded-pill px-2.5 py-1 fw-bold">
                                                <i class="fas fa-tools me-1"></i> MANTE #{{ $c->id_referencia_pago }}
                                            </span>
                                        @else
                                            <span class="badge bg-purple-soft text-purple rounded-pill px-2.5 py-1 fw-bold">
                                                <i class="fas fa-hand-holding-usd me-1"></i> EXPENSAS #{{ $c->id_referencia_pago }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- MINIATURA DE IMAGEN -->
                                    <td class="text-center">
                                        <a href="{{ asset('storage/' . $c->ruta_imagen) }}" target="_blank" title="Ver comprobante en tamaño completo">
                                            <img src="{{ asset('storage/' . $c->ruta_imagen) }}" class="rounded shadow-sm border comprobante-thumb" alt="Foto">
                                        </a>
                                    </td>

                                    <!-- ACCIÓN O ESTADO (DINÁMICO) -->
                                    <td class="text-center">
                                        @if($c->estado == 'Pendiente')
                                            <div class="d-flex justify-content-center align-items-center gap-1">
                                                <!-- BOTÓN VALIDAR -->
                                                <form action="{{ route('admin.comprobante.validar', $c->id_comprobante) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" title="Confirmar y marcar como pagado">
                                                        <i class="fas fa-check me-1"></i> Validar
                                                    </button>
                                                </form>

                                                <!-- BOTÓN RECHAZAR -->
                                                <form action="{{ route('admin.comprobante.rechazar', $c->id_comprobante) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Rechazar comprobante">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif($c->estado == 'Validado')
                                            <!-- ESTADO YA VALIDADO -->
                                            <span class="badge bg-success-soft text-success rounded-pill px-3 py-1.5 fw-bold">
                                                <i class="fas fa-check-circle me-1"></i> Validado
                                            </span>
                                        @else
                                            <!-- ESTADO RECHAZADO -->
                                            <span class="badge bg-danger-soft text-danger rounded-pill px-3 py-1.5 fw-bold">
                                                <i class="fas fa-times-circle me-1"></i> Rechazado
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fas fa-images fa-3x mb-3 opacity-25"></i>
                                        <p class="mb-0">No se han recibido nuevos comprobantes.</p>
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
</div>


@endsection