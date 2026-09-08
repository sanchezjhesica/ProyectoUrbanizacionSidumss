@extends('layouts.propietario')

@section('content')
<div class="seguridad-stellar">
    <!-- CABECERA -->
    <div class="mb-4 border-bottom border-white border-opacity-25 pb-4">
        <h2 class="text-stellar-blue mb-0 fw-bold"><i class="fas fa-shield-alt me-2"></i> Seguridad de la Cuenta</h2>
        <p class="text-muted mb-0 small">Actualice su contraseña periódicamente para mantener su cuenta segura.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 card-glass">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('propietario.password.update') }}" method="POST">
                        @csrf
                        
                        <!-- Contraseña Actual -->
                        <div class="mb-4">
                            <label class="form-label-stellar">Contraseña Actual</label>
                            <input type="password" name="current_password" class="form-control form-stellar @error('current_password') is-invalid @enderror" placeholder="••••••••" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="opacity-10 my-4">

                        <!-- Nueva Contraseña -->
                        <div class="mb-4">
                            <label class="form-label-stellar">Nueva Contraseña</label>
                            <input type="password" name="new_password" class="form-control form-stellar @error('new_password') is-invalid @enderror" placeholder="Mínimo 6 caracteres" required>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirmar Nueva Contraseña -->
                        <div class="mb-4">
                            <label class="form-label-stellar">Confirmar Nueva Contraseña</label>
                            <input type="password" name="new_password_confirmation" class="form-control form-stellar" placeholder="Repita la nueva contraseña" required>
                        </div>

                        <div class="pt-3">
                            <button type="submit" class="btn btn-stellar-blue w-100 py-3 fw-bold shadow">
                                <i class="fas fa-save me-2"></i> ACTUALIZAR CONTRASEÑA
                            </button>
                        </div>
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

    .card-glass {
        background: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }

    .form-label-stellar { 
        font-size: 0.75rem; 
        text-transform: uppercase; 
        font-weight: 800; 
        color: #777; 
        margin-bottom: 8px; 
        display: block; 
    }

    .form-stellar { 
        border-radius: 10px; 
        border: 1px solid #ddd; 
        padding: 12px; 
        background: #fdfdfd; 
        transition: 0.3s;
    }

    .form-stellar:focus {
        border-color: var(--stellar-blue);
        box-shadow: 0 0 0 0.25rem rgba(14, 92, 173, 0.1);
        background: white;
    }

    .btn-stellar-blue { 
        background: var(--stellar-button); 
        color: white !important; 
        border: none; 
        border-radius: 50px; 
        font-weight: 700; 
        letter-spacing: 1px;
    }

    .btn-stellar-blue:hover { opacity: 0.9; transform: translateY(-2px); }
</style>
@endsection