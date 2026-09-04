@extends('layouts.admin')

@section('content')
<div class="viviendas-stellar">
    <!-- 1. ENCABEZADO RESPONSIVO -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 border-bottom pb-4 gap-3">
        <div>
            <h2 class="text-stellar-blue mb-0 fw-bold"><i class="fas fa-city me-2"></i> Viviendas</h2>
            <p class="text-muted mb-0 small">Asigne y gestione los responsables de pago.</p>
        </div>
    </div>

    <!-- 2. ALERTA DE ÉXITO -->
    @if(session('success'))
        <div class="alert alert-stellar alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 3. TABLA OPTIMIZADA PARA MÓVIL -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 ps-md-4 py-3 uppercase-tracking">Vivienda</th>
                            <th class="py-3 uppercase-tracking d-none d-sm-table-cell">Medidor</th>
                            <th class="py-3 uppercase-tracking">Responsable</th>
                            <th class="py-3 text-center uppercase-tracking">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($viviendas as $v)
                        <tr>
                            <!-- Columna Casa: Unificamos Casa + Medidor en móvil -->
                            <td class="ps-3 ps-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="property-icon-stellar d-none d-md-flex me-3">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block">Casa #{{ $v->nro_casa }}</span>
                                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.6rem;">{{ $v->tipo_vivienda }}</small>
                                        <!-- Medidor visible solo en móvil aquí debajo -->
                                        <div class="d-sm-none mt-1">
                                            <span class="badge-medidor-xs">{{ $v->nro_medidor }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Columna Medidor: Solo en tablets y laptops -->
                            <td class="d-none d-sm-table-cell">
                                <span class="badge-medidor">{{ $v->nro_medidor }}</span>
                            </td>

                            <!-- Columna Responsable -->
                            <td>
                                @if($v->propietario)
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark name-truncate">{{ $v->propietario->nombre }} {{ $v->propietario->apellido_paterno }}</span>
                                        <small class="text-muted" style="font-size: 0.7rem;">CI: {{ $v->propietario->ci }}</small>
                                    </div>
                                @else
                                    <span class="badge-stellar bg-danger-soft text-danger">
                                        <i class="fas fa-user-slash me-1" style="font-size: 0.6rem;"></i> <span class="d-none d-md-inline">SIN PROPIETARIO</span><span class="d-md-none">S/N</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Columna Acciones -->
                            <td class="text-center pe-3">
                                <a href="{{ route('admin.viviendas.edit', $v->id_vivienda) }}" class="btn-designar-responsive" title="Designar Dueño">
                                    <i class="fas fa-user-tag"></i>
                                    <span class="d-none d-lg-inline ms-1">Designar</span>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --stellar-blue: #0e5cad;
        --stellar-grad: linear-gradient(45deg, #79f1a4 15%, #0e5cad 85%);
    }

    .text-stellar-blue { color: var(--stellar-blue); }
    .bg-blue-soft { background-color: rgba(14, 92, 173, 0.08); }
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
    
    .uppercase-tracking { font-size: 0.6rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; color: #888; }

    /* Icono Medidor Tablet/Laptop */
    .badge-medidor {
        background-color: #eef2f7;
        color: var(--stellar-blue);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        border: 1px solid #d1d9e6;
    }

    /* Icono Medidor Móvil */
    .badge-medidor-xs {
        background: #f1f5f9;
        color: #64748b;
        font-size: 0.65rem;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
    }

    /* Icono de Propiedad con Degradado */
    .property-icon-stellar {
        width: 35px; height: 35px;
        background: var(--stellar-grad);
        color: white; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.9rem;
    }

    /* Badges */
    .badge-stellar {
        padding: 4px 8px;
        border-radius: 50px;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        white-space: nowrap;
    }

    /* Botón Designar Adaptativo */
    .btn-designar-responsive {
        background-color: white;
        color: var(--stellar-blue);
        border: 1px solid var(--stellar-blue);
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        text-decoration: none;
        transition: 0.3s;
        display: inline-flex;
        align-items: center;
    }

    .btn-designar-responsive:hover {
        background-color: var(--stellar-blue);
        color: white;
    }

    /* Truncar nombres largos en móvil */
    .name-truncate {
        max-width: 100px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @media (min-width: 768px) {
        .name-truncate { max-width: none; }
        .uppercase-tracking { font-size: 0.7rem; }
    }

    /* Ajustes específicos para pantallas muy pequeñas */
    @media (max-width: 480px) {
        .table td { padding: 12px 5px !important; }
        .btn-designar-responsive { 
            width: 32px; height: 32px; 
            padding: 0; justify-content: center;
        }
        .btn-designar-responsive i { font-size: 0.9rem; }
    }

    .rounded-4 { border-radius: 1.25rem !important; }
</style>
@endsection