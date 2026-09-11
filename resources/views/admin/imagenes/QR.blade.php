@extends('layouts.admin')

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
                                    <th class="uppercase-tracking text-center" style="width: 140px;">Acción / Estado</th>
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

<style>
    :root {
        --stellar-blue: #0e5cad;
        --stellar-button: linear-gradient(45deg, #22349e 0%, #8183e6 100%);
    }

    .text-stellar-blue { color: var(--stellar-blue); }
    .bg-blue-soft { background-color: rgba(14, 92, 173, 0.08); }
    .bg-success-soft { background-color: rgba(16, 185, 129, 0.12); color: #059669 !important; }
    .bg-danger-soft { background-color: rgba(239, 68, 68, 0.12); color: #dc2626 !important; }
    .bg-info-soft { background-color: rgba(14, 165, 233, 0.12); color: #0284c7 !important; }
    .bg-warning-soft { background-color: rgba(245, 158, 11, 0.15); }
    .text-warning-dark { color: #b45309 !important; }
    .bg-purple-soft { background-color: rgba(139, 92, 246, 0.12); }
    .text-purple { color: #7c3aed !important; }

    .uppercase-tracking { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1.2px; font-weight: 700; color: #888; }

    /* Previsualización del QR */
    .qr-preview-container {
        width: 100%;
        max-width: 250px;
        min-height: 250px;
        position: relative;
    }
    
    .placeholder-qr {
        width: 100%;
        height: 250px;
        background: #f8f9fa;
    }

    .comprobante-thumb {
        width: 48px;
        height: 48px;
        object-fit: cover;
        transition: transform 0.2s ease;
    }
    .comprobante-thumb:hover {
        transform: scale(1.15);
    }

    /* Inputs y Botones */
    .form-stellar { border-radius: 10px; border: 1px solid #eee; padding: 10px; font-size: 0.85rem; }
    .btn-stellar-blue { 
        background: var(--stellar-button); 
        color: white !important; 
        border: none; 
        border-radius: 50px; 
        transition: 0.3s; 
    }
    .btn-stellar-blue:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(34, 52, 158, 0.3); }

    .rounded-4 { border-radius: 1.25rem !important; }
</style>
@endsection