@extends('layouts.operador')

@push('styles')
    @vite('resources/css/views/operador/lecturas_form.css')
@endpush

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
                                    <small class="text-muted d-block text-uppercase fw-bold view-style-1ed2f53daf" >Lectura Anterior</small>
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



<script>
    const viviendas = @json($viviendas);
    const inputBuscarCasa = document.getElementById('input-buscar-casa');
    const inputLecturaActual = document.querySelector('input[name="lectura_actual"]');
    const infoCard = document.getElementById('vivienda-info-card');
    const mensajeError = document.getElementById('mensaje-busqueda');

    let lecturaAnteriorActual = 0;

    // 1. Al buscar la casa
    inputBuscarCasa.addEventListener('input', function() {
        const nroCasa = this.value;
        const viviendaEncontrada = viviendas.find(v => v.nro_casa == nroCasa);

        if (viviendaEncontrada) {
            infoCard.classList.remove('d-none');
            mensajeError.classList.add('d-none');

            let nombreDueno = 'SIN PROPIETARIO';
            if (viviendaEncontrada.propietario) {
                nombreDueno = (viviendaEncontrada.propietario.nombre + ' ' + (viviendaEncontrada.propietario.apellido_paterno || '')).trim();
            }

            document.getElementById('info-casa-titulo').innerText = 'Casa #' + viviendaEncontrada.nro_casa;
            document.getElementById('info-propietario').innerText = nombreDueno;
            document.getElementById('info-medidor').innerText = viviendaEncontrada.nro_medidor;
            
            // Guardamos la lectura anterior
            lecturaAnteriorActual = parseFloat(viviendaEncontrada.ultima_lectura || 0);
            document.getElementById('valor-anterior').innerText = lecturaAnteriorActual.toFixed(2);
            document.getElementById('id_vivienda_hidden').value = viviendaEncontrada.id_vivienda;

            // Bloquear que se pueda ingresar un valor menor en el input
            inputLecturaActual.min = (lecturaAnteriorActual + 0.01).toFixed(2);
            validarLecturaEnVivo();
        } else {
            infoCard.classList.add('d-none');
            document.getElementById('id_vivienda_hidden').value = '';
            lecturaAnteriorActual = 0;
            if (nroCasa !== "") {
                mensajeError.classList.remove('d-none');
            }
        }
    });

    // 2. Validación en vivo mientras el operador escribe la nueva lectura
    inputLecturaActual.addEventListener('input', validarLecturaEnVivo);

    function validarLecturaEnVivo() {
        const valorIngresado = parseFloat(inputLecturaActual.value);
        let errorSpan = document.getElementById('error-lectura-menor');

        // Crear mensaje de error si no existe
        if (!errorSpan) {
            errorSpan = document.createElement('div');
            errorSpan.id = 'error-lectura-menor';
            errorSpan.className = 'text-danger fw-bold small mt-2 d-none';
            inputLecturaActual.parentElement.parentElement.appendChild(errorSpan);
        }

        if (!isNaN(valorIngresado) && valorIngresado <= lecturaAnteriorActual && lecturaAnteriorActual > 0) {
            errorSpan.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i> La nueva lectura debe ser superior a ${lecturaAnteriorActual.toFixed(2)} m³`;
            errorSpan.classList.remove('d-none');
            inputLecturaActual.classList.add('is-invalid');
        } else {
            errorSpan.classList.add('d-none');
            inputLecturaActual.classList.remove('is-invalid');
        }
    }
</script>
@endsection