<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\ViviendaController;
use App\Http\Controllers\Admin\LecturaController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\EgresoController;
use App\Http\Controllers\Admin\TarifaController;
use App\Http\Controllers\Operador\OperadorController;
use App\Http\Controllers\Propietario\PropietarioController;

// --- RUTAS PÚBLICAS Y AUTENTICACIÓN ---
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    // Ruta compartida para ver recibos detallados (Dashboard)
    Route::get('/recibo/detalle/{id_cobro}', [LecturaController::class, 'showRecibo'])->name('compartido.recibo');

    // =========================================================
    // --- GRUPO ADMINISTRADOR (id_rol = 1) ---
    // =========================================================
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        
        // Gestión de Usuarios
        Route::resource('usuarios', UsuarioController::class)->names('admin.usuarios');
        Route::post('/usuarios/{id}/reset-password', [UsuarioController::class, 'resetPassword'])->name('admin.usuarios.reset');
        
        // Gestión de Viviendas
        Route::resource('viviendas', ViviendaController::class)->names('admin.viviendas');
        
        // Pagos Manuales (Cobrar)
        Route::post('/pagar/agua/{id}', [AdminController::class, 'registrarPagoAgua'])->name('admin.pagar.agua');
        Route::post('/pagar/mantenimiento/{id}', [AdminController::class, 'registrarPagoMantenimiento'])->name('admin.pagar.mantenimiento');
        Route::post('/pagar/remesas', [AdminController::class, 'registrarPagoRemesas'])->name('admin.pagar.remesas');

        // Recursos (Lecturas y Egresos)
        Route::resource('lecturas', LecturaController::class)->names('admin.lecturas');
        Route::resource('egresos', EgresoController::class)->names('admin.egresos');

        // =========================================================
        // RUTAS DE DESCARGA PDF PARA ADMIN (CORREGIDAS)
        // =========================================================
        Route::get('/descargar/agua/{id}', [LecturaController::class, 'imprimirAgua'])->name('admin.descargar.agua');
        Route::get('/descargar/mantenimiento/{id}', [LecturaController::class, 'imprimirMantenimiento'])->name('admin.descargar.mantenimiento');
        Route::get('/descargar/remesas/{id}', [LecturaController::class, 'imprimirRemesas'])->name('admin.descargar.remesas');

        // Módulo de Reportes
        Route::prefix('reportes')->group(function () {
            Route::get('/general', [ReporteController::class, 'index'])->name('admin.reportes.general');
            Route::get('/vivienda', [ReporteController::class, 'porVivienda'])->name('admin.reportes.vivienda');
            Route::get('/morosidad', [ReporteController::class, 'morosidad'])->name('admin.reportes.morosidad');
            
            // Descargas de Reportes
            Route::get('/general/descargar', [ReporteController::class, 'descargarGeneral'])->name('admin.reportes.general.descargar');
            Route::get('/morosidad/descargar', [ReporteController::class, 'descargarMorosidad'])->name('admin.reportes.morosidad.descargar');
            Route::get('/vivienda/{id}/descargar', [ReporteController::class, 'descargarPorVivienda'])->name('admin.reportes.vivienda.descargar');
        });

        // Comprobantes y Gestión de QR
        Route::get('/imagenes-recibidas', [AdminController::class, 'imagenesRecibidas'])->name('admin.reportes.imagenes');
        Route::post('/validar-comprobante/{id}', [AdminController::class, 'validarComprobante'])->name('admin.comprobante.validar');
        Route::post('/rechazar-comprobante/{id}', [AdminController::class, 'rechazarComprobante'])->name('admin.comprobante.rechazar');
        Route::post('/tarifas/update-qr', [TarifaController::class, 'updateQR'])->name('admin.tarifas.updateQR');

        // Configuración de Tarifas
        Route::get('/configuracion/tarifas', [TarifaController::class, 'edit'])->name('admin.tarifas.edit');
        Route::post('/tarifas/global', [TarifaController::class, 'updateGlobal'])->name('admin.tarifas.updateGlobal');
        Route::put('/tarifas/remesa/{id}', [TarifaController::class, 'updateRemesa'])->name('admin.tarifas.updateRemesa');
        Route::put('/tarifas/area/{id}', [TarifaController::class, 'updateArea'])->name('admin.tarifas.updateArea');
    });

    // =========================================================
    // --- GRUPO OPERADOR ---
    // =========================================================
    Route::middleware(['operador'])->prefix('operador')->group(function () {
            // 1. Vista del Dashboard (Historial)
    Route::get('/dashboard', [OperadorController::class, 'index'])->name('operador.dashboard');
    
    // 2. ESTA ES LA RUTA QUE TE FALTA Y CAUSA EL ERROR:
    Route::get('/lecturas/nueva', [OperadorController::class, 'nuevaLectura'])->name('operador.lecturas.crear');
    
    // 3. Acción de Guardar
    Route::post('/lecturas/guardar', [OperadorController::class, 'guardarLectura'])->name('operador.lecturas.store');
    });

    // =========================================================
    // --- GRUPO PROPIETARIO ---
    // =========================================================
    Route::middleware(['propietario'])->prefix('propietario')->group(function () {
        Route::get('/dashboard', [PropietarioController::class, 'index'])->name('propietario.dashboard');
        Route::get('/mis-avisos', [PropietarioController::class, 'misAvisos'])->name('propietario.avisos');
        Route::get('/reservas', [PropietarioController::class, 'misReservas'])->name('propietario.reservas.index');
        Route::post('/reservas/nueva', [PropietarioController::class, 'guardarReserva'])->name('propietario.reservas.store');
        
        // Subida de Comprobante QR
        Route::post('/subir-comprobante', [PropietarioController::class, 'subirComprobante'])->name('propietario.comprobante.store');

        // Vistas de Avisos
        Route::get('/aviso-agua/{id}', [PropietarioController::class, 'verAvisoAgua'])->name('propietario.aviso.agua');
        Route::get('/aviso-mantenimiento/{id}', [PropietarioController::class, 'verAvisoMantenimiento'])->name('propietario.aviso.mantenimiento');
        Route::get('/aviso-remesas/{id}', [PropietarioController::class, 'verAvisoRemesas'])->name('propietario.aviso.remesas');

        // Descargas PDF Propietario
        Route::get('/descargar/agua/{id}', [PropietarioController::class, 'descargarAvisoAgua'])->name('propietario.descargar.agua');
        Route::get('/descargar/mantenimiento/{id}', [PropietarioController::class, 'descargarAvisoMantenimiento'])->name('propietario.descargar.mantenimiento');
        Route::get('/descargar/remesas/{id}', [PropietarioController::class, 'descargarAvisoRemesas'])->name('propietario.descargar.remesas');
            // Rutas para cambio de contraseña
        Route::get('/perfil/seguridad', [PropietarioController::class, 'editPassword'])->name('propietario.password.edit');
        Route::post('/perfil/seguridad', [PropietarioController::class, 'updatePassword'])->name('propietario.password.update');

    });
});