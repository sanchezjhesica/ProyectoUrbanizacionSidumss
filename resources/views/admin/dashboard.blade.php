@extends('layouts.admin')

@push('styles')
    @vite('resources/css/views/admin/dashboard.css')
@endpush

@section('content')
<div class="dashboard-stellar">
    <!-- 1. ENCABEZADO -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h2 class="text-stellar-blue fw-bold mb-0"><i class="fas fa-tachometer-alt me-2"></i> Panel de Control</h2>
            <p class="text-muted mb-0">Resumen general de la Urbanización</p>
        </div>
        <div class="d-none d-md-block">
            <span class="badge bg-blue-soft text-stellar-blue p-2 px-3 rounded-pill fw-bold">
                <i class="far fa-calendar-alt me-1"></i> {{ date('d/m/Y') }}
            </span>
        </div>
    </div>

<!-- 2. CARRUSEL DE IMÁGENES -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div id="carouselUrbanizacion" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselUrbanizacion" data-bs-slide-to="0" class="active"></button>
                    <button type="button" data-bs-target="#carouselUrbanizacion" data-bs-slide-to="1"></button>
                    <button type="button" data-bs-target="#carouselUrbanizacion" data-bs-slide-to="2"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="{{ asset('img/img1.jpeg') }}" class="d-block w-100 img-carousel-hero" alt="Imagen 1">
                        <div class="carousel-caption custom-caption">
                            <h4 class="fw-bold mb-0">SIDUMSS NORTE A</h4>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('img/img2.jpeg') }}" class="d-block w-100 img-carousel-hero" alt="Imagen 2">
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('img/img3.jpeg') }}" class="d-block w-100 img-carousel-hero" alt="Imagen 3">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- 3. SECCIÓN DE FINANZAS (SIN FONDO, CON TARJETAS LIMPIAS) -->
    <div class="row g-4 mb-5">
  <!-- Ingresos Totales -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 border-bottom border-success border-5 h-100 finance-card">
                <div class="card-body p-4 text-center">
                    <div class="icon-circle bg-success-soft text-success mb-3 mx-auto">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <h6 class="text-uppercase small fw-bold text-muted letter-spacing-1">Ingresos Totales</h6>
                    
                    <!-- TOTAL CON RESERVAS INCLUIDAS -->
                    <h3 class="fw-bold text-dark mb-0">Bs. {{ number_format($ingresos, 2) }}</h3>

                    <!-- NOTA INFORMATIVA SI HAY RESERVAS COBRADAS -->
                    @if(($ingresosReservas ?? 0) > 0)
                        <small class="text-success fw-bold d-block mt-1 view-style-dbc8d1cddf" >
                            <i class="fas fa-check-circle"></i> Inc. Bs. {{ number_format($ingresosReservas, 2) }} por áreas recreativas
                        </small>
                    @endif
                </div>
            </div>
        </div>

        <!-- Egresos -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 border-bottom border-danger border-5 h-100 finance-card">
                <div class="card-body p-4 text-center">
                    <div class="icon-circle bg-danger-soft text-danger mb-3 mx-auto">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <h6 class="text-uppercase small fw-bold text-muted letter-spacing-1">Egresos Registrados</h6>
                    <h3 class="fw-bold text-dark mb-0">Bs. {{ number_format($egresos, 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Saldo -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 border-bottom border-stellar-blue border-5 h-100 finance-card">
                <div class="card-body p-4 text-center">
                    <div class="icon-circle bg-blue-soft text-stellar-blue mb-3 mx-auto">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h6 class="text-uppercase small fw-bold text-muted letter-spacing-1">Saldo Disponible</h6>
                    <h3 class="fw-bold text-dark mb-0">Bs. {{ number_format($saldo, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. GESTIÓN DE EGRESOS -->
    <div class="row g-4">
        <!-- Formulario -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                    <div class="icon-header-blue me-2"><i class="fas fa-plus"></i></div>
                    <h5 class="mb-0 text-stellar-blue fw-bold">Nuevo Gasto</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.egresos.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label-stellar">Concepto</label>
                            <input type="text" name="descripcion" class="form-control form-stellar" placeholder="Descripción del gasto" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label-stellar">Monto (Bs.)</label>
                                <input type="number" step="0.01" name="monto" class="form-control form-stellar" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label-stellar">Fecha</label>
                                <input type="date" name="fecha_egreso" class="form-control form-stellar" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label-stellar">Categoría</label>
                            <select name="categoria" class="form-select form-stellar">
                                <option value="Sueldos">Sueldos</option>
                                <option value="Mantenimiento">Mantenimiento</option>
                                <option value="Servicios Publicos">Servicios Públicos</option>
                                <option value="Otros">Otros</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-stellar-blue w-100 py-2 fw-bold">GUARDAR GASTO</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-history me-2"></i> Egresos Recientes</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 uppercase-tracking">Fecha</th>
                                <th class="py-3 uppercase-tracking">Concepto</th>
                                <th class="text-end pe-4 py-3 uppercase-tracking">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimosEgresos as $eg)
                            <tr>
                                <td class="ps-4 small text-muted">{{ \Carbon\Carbon::parse($eg->fecha_egreso)->format('d/m/y') }}</td>
                                <td class="fw-bold text-dark">{{ $eg->descripcion }}</td>
                                <td class="text-end pe-4 text-danger fw-bold">Bs. {{ number_format($eg->monto, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection