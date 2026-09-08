@extends('layouts.operador')

@section('content')
<div class="operador-stellar">
    <!-- 1. ENCABEZADO -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 mb-md-5 border-bottom pb-4 gap-3">
        <div>
            <h2 class="text-stellar-blue mb-0 fw-bold"><i class="fas fa-tasks me-2"></i> Registro de consumo de agua</h2>
            <p class="text-muted mb-0 small">Ingrese el número de casa para registrar el consumo.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-stellar alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-stellar-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <strong>Atención:</strong> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom py-4 text-center">
                    <h4 class="mb-0 text-stellar-blue fw-bold"><i class="fas fa-search me-2"></i> Buscar Casa</h4>
                </div>
                <div class="card-body p-4 p-md-5">
                    
                    <form action="{{ route('operador.lecturas.store') }}" method="POST" id="form-lectura">
                        @csrf
                        
                        <!-- BUSCADOR POR NÚMERO DE CASA -->
                        <div class="mb-4">
                            <label class="form-label-stellar">Escriba el Número de Casa</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light"><i class="fas fa-hashtag text-muted"></i></span>
                                <input type="number" id="input-buscar-casa" class="form-control form-stellar fw-bold" placeholder="Ej: 45" autocomplete="off">
                            </div>
                            <div id="mensaje-busqueda" class="small mt-2 text-danger d-none">No se encontró ninguna casa con ese número.</div>
                        </div>

                        <!-- CUADRO DE CONFIRMACIÓN DE VIVIENDA (Se llena con JS) -->
                        <div id="vivienda-info-card" class="d-none mb-4 p-4 rounded-4 bg-light border-start border-5 border-stellar-blue animate__animated animate__fadeIn">
                            <div class="row align-items-center">
                                <div class="col-sm-8">
                                    <h5 class="fw-bold mb-1" id="info-casa-titulo">Casa #0</h5>
                                    <p class="mb-1 text-dark">Propietario: <b id="info-propietario">---</b></p>
                                    <p class="mb-0 text-muted small">Medidor: <span id="info-medidor">---</span></p>
                                </div>
                                <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.6rem;">Lectura Anterior</small>
                                    <h3 class="mb-0 fw-bold text-stellar-blue"><span id="valor-anterior">0</span> <small class="fw-normal fs-6">m³</small></h3>
                                </div>
                            </div>
                            <!-- Input oculto para el ID real de la vivienda -->
                            <input type="hidden" name="id_vivienda" id="id_vivienda_hidden">
                        </div>

                        <!-- SELECCIÓN DE PERIODO -->
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label-stellar">Mes de Cobro</label>
                                <select name="mes" class="form-select form-stellar" required>
                                    @php $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']; @endphp
                                    @foreach($meses as $index => $mes)
                                        <option value="{{ $index + 1 }}" {{ (now()->month == $index + 1) ? 'selected' : '' }}>{{ $mes }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label-stellar">Año</label>
                                <input type="number" name="anio" class="form-control form-stellar" value="{{ now()->year }}" required>
                            </div>
                        </div>

                        <!-- LECTURA ACTUAL -->
                        <div class="mb-5">
                            <label class="form-label-stellar">Nueva Lectura del Medidor (m³)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-tint text-info"></i></span>
                                <input type="number" name="lectura_actual" step="0.01" class="form-control form-stellar border-start-0 fs-4 fw-bold text-stellar-blue" placeholder="0.00" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-stellar-blue btn-lg w-100 py-3 shadow-sm fw-bold">
                            <i class="fas fa-save me-2"></i> GUARDAR Y GENERAR COBROS
                        </button>
                    </form>
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
    .form-label-stellar { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; color: #777; margin-bottom: 8px; display: block; }

    .form-stellar { border: 1px solid #e0e0e0; border-radius: 10px; padding: 12px; transition: 0.3s; }
    .form-stellar:focus { border-color: var(--stellar-blue); box-shadow: 0 0 0 0.25rem rgba(14, 92, 173, 0.1); }

    .btn-stellar-blue {
        background: var(--stellar-button); color: white !important;
        border-radius: 50px; font-weight: 700; border: none;
        text-transform: uppercase; letter-spacing: 1px; transition: 0.3s;
    }
    .btn-stellar-blue:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(34, 52, 158, 0.3); }

    .alert-stellar-danger { background: #fff; border-left: 5px solid #e74c3c; color: #e74c3c; border-radius: 8px; }
    .rounded-4 { border-radius: 1.25rem !important; }
</style>

<script>
    // PASAMOS LA LISTA DE VIVIENDAS DE PHP A JAVASCRIPT
    const viviendas = @json($viviendas);

    document.getElementById('input-buscar-casa').addEventListener('input', function() {
        const nroCasa = this.value;
        const infoCard = document.getElementById('vivienda-info-card');
        const mensajeError = document.getElementById('mensaje-busqueda');
        
        // Buscar la vivienda en el array
        const viviendaEncontrada = viviendas.find(v => v.nro_casa == nroCasa);

        if (viviendaEncontrada) {
            infoCard.classList.remove('d-none');
            mensajeError.classList.add('d-none');
            
            // Llenar datos
            document.getElementById('info-casa-titulo').innerText = 'Casa #' + viviendaEncontrada.nro_casa;
            document.getElementById('info-propietario').innerText = viviendaEncontrada.propietario ? (viviendaEncontrada.propietario.nombre + ' ' + viviendaEncontrada.propietario.apellido_paterno) : 'SIN PROPIETARIO';
            document.getElementById('info-medidor').innerText = viviendaEncontrada.nro_medidor;
            document.getElementById('valor-anterior').innerText = viviendaEncontrada.ultima_lectura;
            document.getElementById('id_vivienda_hidden').value = viviendaEncontrada.id_vivienda;
        } else {
            infoCard.classList.add('d-none');
            document.getElementById('id_vivienda_hidden').value = '';
            if(nroCasa !== "") {
                mensajeError.classList.remove('d-none');
            }
        }
    });
</script>
@endsection