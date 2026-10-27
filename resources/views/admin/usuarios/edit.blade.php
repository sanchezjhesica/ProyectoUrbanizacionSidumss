@extends('layouts.admin')

@push('styles')
    @vite('resources/css/views/admin/usuarios/edit.css')
@endpush

@section('content')
<div class="usuarios-edit-stellar">
    <!-- 1. CABECERA RESPONSIVA -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 mb-md-5 border-bottom pb-4 gap-3">
        <div>
            <h2 class="text-stellar-blue mb-0 fw-bold"><i class="fas fa-user-edit me-2"></i> Editar Propietario</h2>
            <p class="text-muted mb-0">Modifique el perfil de <strong>{{ $usuario->nombre }} {{ $usuario->apellido_paterno }}</strong>.</p>
        </div>
    </div>

    <!-- 2. TARJETA DEL FORMULARIO -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('admin.usuarios.update', $usuario->id_usuario) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Sección: Identidad -->
                <div class="form-section mb-5">
                    <h5 class="text-stellar-blue fw-bold mb-4 border-bottom pb-2">
                        <i class="fas fa-id-card-alt me-2"></i>Información de Identidad
                    </h5>
                    
                    <div class="mb-4">
                        <label class="form-label-stellar">Nombre Completo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" name="nombre" class="form-control form-stellar border-start-0" value="{{ $usuario->nombre }}" required>
                        </div>
                    </div>

                    <div class="row g-3 g-md-4">
                        <div class="col-md-6">
                            <label class="form-label-stellar">Apellido Paterno</label>
                            <input type="text" name="apellido_paterno" class="form-control form-stellar" value="{{ $usuario->apellido_paterno }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-stellar">Apellido Materno</label>
                            <input type="text" name="apellido_materno" class="form-control form-stellar" value="{{ $usuario->apellido_materno }}">
                        </div>
                    </div>
                </div>

                <!-- Sección: Contacto -->
                <div class="form-section mb-5">
                    <h5 class="text-stellar-blue fw-bold mb-4 border-bottom pb-2">
                        <i class="fas fa-address-book me-2"></i>Documentación y Contacto
                    </h5>
                    <div class="row g-3 g-md-4">
                        <div class="col-md-6">
                            <label class="form-label-stellar">CI (Cédula de Identidad)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-fingerprint text-muted"></i></span>
                                <input type="text" name="ci" class="form-control form-stellar border-start-0" value="{{ $usuario->ci }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-stellar">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control form-stellar border-start-0" value="{{ $usuario->email }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. BOTONES DE ACCIÓN -->
                <div class="pt-4 border-top d-flex flex-column flex-md-row gap-3">
                    <button type="submit" class="btn btn-stellar-submit px-5 shadow-sm order-2 order-md-1">
                        <i class="fas fa-save me-2"></i> Guardar Cambios
                    </button>
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-light rounded-pill px-4 order-1 order-md-2">
                        Descartar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection