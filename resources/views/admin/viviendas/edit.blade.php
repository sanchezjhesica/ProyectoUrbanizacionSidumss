@extends('layouts.admin')

@push('styles')
    @vite('resources/css/views/admin/viviendas/edit.css')
@endpush

@section('content')
<div class="viviendas-edit-stellar">
    <!-- 1. CABECERA RESPONSIVA -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 mb-md-5 border-bottom pb-4 gap-3">
        <div>
            <h2 class="text-stellar-blue mb-0 fw-bold"><i class="fas fa-user-tag me-2"></i> Designar Propietario</h2>
            <p class="text-muted mb-0">Gestión de responsabilidad para la <b class="text-dark">Casa {{ $vivienda->nro_casa }}</b></p>
        </div>
    </div>

    <form action="{{ route('admin.viviendas.update', $vivienda->id_vivienda) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- COLUMNA IZQUIERDA: BUSCADOR DE PROPIETARIO -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-light-soft">
                    <div class="card-body p-4">
                        <h5 class="text-stellar-blue fw-bold mb-4"><i class="fas fa-user-check me-2"></i> Asignación de Dueño</h5>
                        
                        <!-- Visualización de la Selección Actual -->
                        <div class="selection-preview mb-4 p-3 rounded-3 bg-white border-start border-stellar-blue border-4 shadow-sm">
                            <small class="text-muted d-block mb-1 text-uppercase fw-bold view-style-2fa492684f" >Asignado actualmente:</small>
                            <div id="display-nombre-seleccionado" class="fw-bold text-stellar-blue fs-5">
                                @if($vivienda->propietario)
                                    {{ $vivienda->propietario->nombre }} {{ $vivienda->propietario->apellido_paterno }} {{ $vivienda->propietario->apellido_materno }}
                                @else
                                    <span class="text-danger">Sin propietario asignado</span>
                                @endif
                            </div>
                            <input type="hidden" name="id_propietario" id="id_propietario_input" value="{{ $vivienda->id_propietario }}">
                        </div>

                        <!-- Campo de Búsqueda -->
                        <div class="mb-3">
                            <label class="form-label-stellar">Filtrar por Nombre o CI</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" id="input-buscador" class="form-control form-stellar border-start-0 shadow-none" placeholder="Escriba para buscar...">
                            </div>
                        </div>

                        <!-- Lista de Usuarios con Scroll -->
                        <div class="owner-list-container shadow-sm border rounded-3 bg-white">
                            <ul class="list-group list-group-flush" id="lista-propietarios">
                                <!-- Opción Vacía -->
                                <li class="list-group-item list-group-item-action owner-item border-0" data-id="" data-nombre="Sin propietario asignado">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle-danger me-3"><i class="fas fa-user-slash text-danger"></i></div>
                                        <div>
                                            <span class="fw-bold d-block text-danger">Quitar Propietario</span>
                                            <small class="text-muted">Dejar vivienda sin responsable</small>
                                        </div>
                                    </div>
                                </li>

                                @foreach($propietarios as $p)
                                    <li class="list-group-item list-group-item-action owner-item border-0 {{ $vivienda->id_propietario == $p->id_usuario ? 'active-selection' : '' }}" 
                                        data-id="{{ $p->id_usuario }}" 
                                        data-nombre="{{ $p->nombre }} {{ $p->apellido_paterno }} {{ $p->apellido_materno }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-circle me-3"><i class="fas fa-user"></i></div>
                                                <div>
                                                    <span class="fw-bold d-block text-item-name">{{ $p->nombre }} {{ $p->apellido_paterno }}</span>
                                                    <small class="text-muted">CI: {{ $p->ci }}</small>
                                                </div>
                                            </div>
                                            <i class="fas fa-check-circle check-icon"></i>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: DATOS TÉCNICOS -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h5 class="text-stellar-blue fw-bold mb-4"><i class="fas fa-tools me-2"></i> Datos Técnicos</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-stellar">Nro. de Casa</label>
                                <input type="text" name="nro_casa" class="form-control form-stellar" value="{{ $vivienda->nro_casa }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-stellar">Nro. de Medidor</label>
                                <input type="text" name="nro_medidor" class="form-control form-stellar" value="{{ $vivienda->nro_medidor }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label-stellar">Calle / Ubicación Interna</label>
                                <input type="text" name="calle" class="form-control form-stellar" value="{{ $vivienda->calle }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label-stellar">Tipo de Propiedad</label>
                                <select name="tipo_vivienda" class="form-select form-stellar">
                                    <option value="Casa" {{ $vivienda->tipo_vivienda == 'Casa' ? 'selected' : '' }}>Casa Habitada</option>
                                    <option value="Lote Vacio" {{ $vivienda->tipo_vivienda == 'Lote Vacio' ? 'selected' : '' }}>Lote Baldío</option>
                                </select>
                            </div>
                        </div>

                        <!-- INFO CARD -->
                        <div class="mt-4 p-3 rounded-3 bg-blue-soft border border-info border-opacity-10">
                            <small class="text-stellar-blue"><i class="fas fa-info-circle me-1"></i> Asegúrese de que el propietario esté previamente registrado en el módulo de usuarios.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="mt-5 pt-4 border-top d-flex flex-column flex-md-row justify-content-end gap-3">
            <a href="{{ route('admin.viviendas.index') }}" class="btn btn-light rounded-pill px-4 order-2 order-md-1">Cancelar</a>
            <button type="submit" class="btn btn-stellar-submit px-5 shadow-sm order-1 order-md-2">
                <i class="fas fa-save me-2"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // 1. Lógica del Buscador
    $("#input-buscador").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#lista-propietarios li").filter(function() {
            if($(this).data('id') === "") return; 
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // 2. Lógica de Selección
    $(".owner-item").on("click", function() {
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');

        $("#id_propietario_input").val(id);

        if(id === "") {
            $("#display-nombre-seleccionado").html('<span class="text-danger">Sin propietario asignado</span>');
        } else {
            $("#display-nombre-seleccionado").text(nombre).removeClass('text-danger');
        }

        $(".owner-item").removeClass('active-selection');
        $(this).addClass('active-selection');
    });
});
</script>
@endsection