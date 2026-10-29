@extends('layouts.propietario')

@push('styles')
    <style>
/* VARIABLES STELLAR GREEN/BLUE */
    :root {
        --stellar-blue: #0e5cad;
        --stellar-grad: linear-gradient(45deg, #79f1a4 15%, #0e5cad 85%);
    }

    .text-stellar-blue { color: var(--stellar-blue); }
    .bg-blue-soft { background-color: rgba(14, 92, 173, 0.08); }
    .bg-light-soft { background-color: #fdfdfd; }
    .bg-stellar-grad { background: var(--stellar-grad); }
    .tracking-widest { letter-spacing: 2px; font-size: 0.7rem; }

    /* Avatar del Hero */
    .user-avatar-large {
        width: 85px;
        height: 85px;
        background: rgba(14, 92, 173, 0.1);
        color: var(--stellar-blue);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }

    /* Icono de Info en la tarjeta de gradiente */
    .info-icon-box {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Items de Propiedad */
    .property-icon-box {
        width: 40px;
        height: 40px;
        background: white;
        color: var(--stellar-blue);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .property-item {
        transition: all 0.3s;
        border: 1px solid #eee !important;
    }
    .property-item:hover {
        border-color: var(--stellar-blue) !important;
        background-color: #fff;
        transform: translateX(5px);
    }

    .rounded-4 { border-radius: 1.25rem !important; }

    /* Ajustes para móviles */
    @media (max-width: 768px) {
        .user-avatar-large { width: 70px; height: 70px; font-size: 2.5rem; }
        .display-6 { font-size: 1.5rem; }
    }

.view-style-21cbd26705 { max-width: 700px !important; }
</style>
@endpush

@section('content')
<div class="propietario-stellar">
    <!-- 1. SECCIÓN DE BIENVENIDA (HERO) -->
    <div class="text-center py-5 mb-4 mb-md-5 border-bottom">
        <div class="user-avatar-large mx-auto mb-4 animate__animated animate__zoomIn">
            <i class="fas fa-user-circle"></i>
        </div>
        <h1 class="display-6 fw-bold text-dark">Bienvenido, {{ Auth::user()->nombre }}</h1>
        <p class="text-muted fs-5 mx-auto px-3 view-style-21cbd26705" >
            Desde su panel personal puede gestionar sus avisos de cobranza, revisar consumos de agua y realizar reservas para las áreas recreativas.
        </p>
    </div>

    <div class="row g-4">
        <!-- 2. MIS PROPIEDADES (Lista Estilizada) -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-stellar-blue"><i class="fas fa-home me-2"></i> Mis Propiedades Registradas</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-4">Usted figura como responsable de las siguientes unidades:</p>
                    
                    @forelse($viviendas as $v)
                        <div class="property-item d-flex align-items-center p-3 mb-3 rounded-3 bg-light-soft border">
                            <div class="property-icon-box me-3">
                                <i class="fas fa-key"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark fs-6">Casa #{{ $v->nro_casa }}</div>
                                <div class="small text-muted">{{ $v->calle ?? 'Urbanización Central' }}</div>
                            </div>
                            <div class="text-end d-none d-sm-block">
                                <span class="badge rounded-pill bg-blue-soft text-stellar-blue px-3">
                                    Medidor: {{ $v->nro_medidor }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-circle text-muted fa-2x mb-2"></i>
                            <p class="text-muted">No tiene propiedades registradas.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 3. INFO ADICIONAL (ESTILO STELLAR) -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-stellar-grad text-white">
                <div class="card-body p-4 p-md-5 d-flex flex-column justify-content-center text-center">
                    <div class="info-icon-box mb-4 mx-auto">
                        <i class="fas fa-file-pdf fa-2x"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Gestión de Avisos</h4>
                    <p class="mb-4 opacity-90">
                        Ahora puede **descargar sus recibos en formato PDF** directamente desde la sección "Mis Avisos". Guarde sus comprobantes de forma segura y ecológica.
                    </p>
                    <div class="mt-4 pt-3 border-top border-white border-opacity-25">
                        <small class="fw-bold text-uppercase tracking-widest opacity-75">SIDUMSS · Gestión Responsable</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection