<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\TipoVehiculoController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\VacacionesController;
use App\Http\Controllers\FuncionController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\TurnosController;
use App\Http\Controllers\GruposPersonalController;
use App\Http\Controllers\ProgramacionesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\MotivoController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
// Ruta pública para registro de asistencia
Route::get('/registro-asistencia', [AsistenciaController::class, 'mostrarRegistro'])->name('asistencias.registrar');
Route::post('/registro-asistencia', [AsistenciaController::class, 'procesarRegistro'])->name('asistencias.registrar.proceso');

// Ruta raíz redirige al dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Rutas protegidas por autenticación
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ========================================
    // MÓDULO DE VEHÍCULOS
    // ========================================
    Route::prefix('vehiculos')->group(function () {
        
        // Marcas
        Route::get('marcas', [MarcaController::class, 'index'])->name('vehiculos.marcas.index');
        Route::post('marcas', [MarcaController::class, 'store'])->name('vehiculos.marcas.store');
        Route::get('marcas/{marca}', [MarcaController::class, 'show'])->name('vehiculos.marcas.show');
        Route::put('marcas/{marca}', [MarcaController::class, 'update'])->name('vehiculos.marcas.update');
        Route::delete('marcas/{marca}', [MarcaController::class, 'destroy'])->name('vehiculos.marcas.destroy');
        // CORREGIDO: Cambiar de /toggle a /toggle-activo
        Route::patch('marcas/{marca}/toggle-activo', [MarcaController::class, 'toggleActivo'])->name('vehiculos.marcas.toggle-activo');
        Route::delete('marcas/{marca}/delete-logo', [MarcaController::class, 'deleteLogo'])->name('vehiculos.marcas.delete-logo'); // ← AGREGAR

        // Tipos de Vehículo
        Route::get('tipos', [TipoVehiculoController::class, 'index'])->name('vehiculos.tipos.index');
        Route::post('tipos', [TipoVehiculoController::class, 'store'])->name('vehiculos.tipos.store');
        Route::get('tipos/{tipo}', [TipoVehiculoController::class, 'show'])->name('vehiculos.tipos.show');
        Route::put('tipos/{tipo}', [TipoVehiculoController::class, 'update'])->name('vehiculos.tipos.update');
        Route::delete('tipos/{tipo}', [TipoVehiculoController::class, 'destroy'])->name('vehiculos.tipos.destroy');
        Route::patch('tipos/{tipo}/toggle-activo', [TipoVehiculoController::class, 'toggleActivo'])->name('vehiculos.tipos.toggle-activo');
        
        // Colores
        Route::get('colores', [ColorController::class, 'index'])->name('vehiculos.colores.index');
        Route::post('colores', [ColorController::class, 'store'])->name('vehiculos.colores.store');
        Route::get('colores/{color}', [ColorController::class, 'show'])->name('vehiculos.colores.show');
        Route::put('colores/{color}', [ColorController::class, 'update'])->name('vehiculos.colores.update');
        Route::delete('colores/{color}', [ColorController::class, 'destroy'])->name('vehiculos.colores.destroy');
        Route::patch('colores/{color}/toggle-activo', [ColorController::class, 'toggleActivo'])->name('vehiculos.colores.toggle-activo');
        
        // Modelos
        Route::get('modelos', [ModeloController::class, 'index'])->name('vehiculos.modelos.index');
        Route::post('modelos', [ModeloController::class, 'store'])->name('vehiculos.modelos.store');
        Route::get('modelos/{modelo}', [ModeloController::class, 'show'])->name('vehiculos.modelos.show');
        Route::put('modelos/{modelo}', [ModeloController::class, 'update'])->name('vehiculos.modelos.update');
        Route::delete('modelos/{modelo}', [ModeloController::class, 'destroy'])->name('vehiculos.modelos.destroy');
        Route::patch('modelos/{modelo}/toggle-activo', [ModeloController::class, 'toggleActivo'])->name('vehiculos.modelos.toggle-activo');
        Route::get('modelos/por-marca/{marca}', [ModeloController::class, 'getPorMarca'])->name('vehiculos.modelos.por-marca');
        
        // Vehículos
        Route::get('vehiculos', [VehiculoController::class, 'index'])->name('vehiculos.vehiculos.index');
        Route::post('vehiculos', [VehiculoController::class, 'store'])->name('vehiculos.vehiculos.store');
        Route::get('vehiculos/{vehiculo}', [VehiculoController::class, 'show'])->name('vehiculos.vehiculos.show');
        Route::put('vehiculos/{vehiculo}', [VehiculoController::class, 'update'])->name('vehiculos.vehiculos.update');
        Route::delete('vehiculos/{vehiculo}', [VehiculoController::class, 'destroy'])->name('vehiculos.vehiculos.destroy');
        Route::patch('vehiculos/{vehiculo}/toggle-activo', [VehiculoController::class, 'toggleActivo'])->name('vehiculos.vehiculos.toggle-activo');
        Route::post('vehiculos/{vehiculo}/toggle-disponible', [VehiculoController::class, 'toggleDisponible'])->name('vehiculos.vehiculos.toggle-disponible');
        
        // Gestión de Imágenes de Vehículos
        Route::post('vehiculos/{vehiculo}/imagenes', [VehiculoController::class, 'uploadImagen'])->name('vehiculos.vehiculos.imagenes.upload');
        Route::get('vehiculos/{vehiculo}/imagenes', [VehiculoController::class, 'getImagenes'])->name('vehiculos.vehiculos.imagenes.get');
        Route::post('vehiculos/{vehiculo}/imagenes/{imagen}/perfil', [VehiculoController::class, 'setImagenPerfil'])->name('vehiculos.vehiculos.imagenes.perfil');
        Route::delete('vehiculos/{vehiculo}/imagenes/{imagen}', [VehiculoController::class, 'deleteImagen'])->name('vehiculos.vehiculos.imagenes.delete');
    });
    // MÓDULO DE GESTIÓN DE ZONAS
    // ========================================
    Route::prefix('gestionzonas')->group(function () {
        // Zonas
        Route::get('zonas', [ZonaController::class, 'index'])->name('zonas.index');
        Route::get('zonas/create', [ZonaController::class, 'create'])->name('zonas.create');
        Route::post('zonas', [ZonaController::class, 'store'])->name('zonas.store');
        Route::get('zonas/{zona}/edit', [ZonaController::class, 'edit'])->name('zonas.edit');
        Route::put('zonas/{zona}', [ZonaController::class, 'update'])->name('zonas.update');
        Route::delete('zonas/{zona}', [ZonaController::class, 'destroy'])->name('zonas.destroy');
        Route::get('zonas/mapa', [ZonaController::class, 'mapaGeneral'])->name('zonas.mapa');
        
        // Validación de superposición de zonas
        Route::post('zonas/check-overlap', [ZonaController::class, 'checkOverlap'])->name('zonas.check-overlap');
    });
    
    // ========================================
    // MÓDULO DE PERSONAL
    // ========================================


    Route::prefix('personal')->name('personal.')->group(function () {
        // Funciones
        Route::get('funciones', [FuncionController::class, 'index'])->name('funciones.index');
        Route::post('funciones', [FuncionController::class, 'store'])->name('funciones.store');
        Route::get('funciones/{funcion}', [FuncionController::class, 'show'])->name('funciones.show');
        Route::put('funciones/{funcion}', [FuncionController::class, 'update'])->name('funciones.update');
        Route::delete('funciones/{funcion}', [FuncionController::class, 'destroy'])->name('funciones.destroy');
        Route::post('funciones/{funcion}/toggle-activo', [FuncionController::class, 'toggleActivo'])->name('funciones.toggle');
        
        // Endpoints adicionales para funciones
        Route::get('funciones-activas', [FuncionController::class, 'getActivas'])->name('funciones.activas');
        Route::get('conductores-ayudantes', [FuncionController::class, 'getConductoresYAyudantes'])->name('funciones.conductores-ayudantes');
        
          // Personal ✅ ESTAS SON LAS RUTAS QUE USA LA VISTA
          /*
    Route::get('personal', [PersonalController::class, 'index'])->name('personal.personal.index');
    Route::post('personal', [PersonalController::class, 'store'])->name('personal.personal.store');
    Route::get('personal/{personal}', [PersonalController::class, 'show'])->name('personal.personal.show');
    Route::put('personal/{personal}', [PersonalController::class, 'update'])->name('personal.personal.update');
    Route::delete('personal/{personal}', [PersonalController::class, 'destroy'])->name('personal.personal.destroy');
    Route::post('personal/{personal}/toggle-activo', [PersonalController::class, 'toggleActivo'])->name('personal.personal.toggle');
    Route::delete('personal/{personal}/delete-foto', [PersonalController::class, 'deleteFoto'])->name('personal.personal.delete-foto');

    // Endpoints adicionales para personal
    Route::get('personal-por-funcion/{funcion}', [PersonalController::class, 'getPorFuncion'])->name('personal.personal.por-funcion');
    Route::get('conductores', [PersonalController::class, 'getConductores'])->name('personal.conductores');
    Route::get('ayudantes', [PersonalController::class, 'getAyudantes'])->name('personal.ayudantes');
    */
     // Rutas principales CRUD
   
        // PERSONAL ✅ RUTAS CORREGIDAS
        Route::prefix('personal')->name('personal.')->group(function () {
            // Rutas principales CRUD
            Route::get('/', [PersonalController::class, 'index'])->name('index');
            Route::post('/', [PersonalController::class, 'store'])->name('store');
            Route::get('/{personal}', [PersonalController::class, 'show'])->name('show');
            Route::put('/{personal}', [PersonalController::class, 'update'])->name('update');
            Route::delete('/{personal}', [PersonalController::class, 'destroy'])->name('destroy');
            
            // Acciones adicionales
            Route::patch('/{personal}/toggle-activo', [PersonalController::class, 'toggleActivo'])->name('toggle-activo');
            Route::delete('/{personal}/delete-foto', [PersonalController::class, 'deleteFoto'])->name('delete-foto');
            
            // APIs para selects dinámicos
            Route::get('/api/conductores', [PersonalController::class, 'getConductores'])->name('conductores');
            Route::get('/api/ayudantes', [PersonalController::class, 'getAyudantes'])->name('ayudantes');
        });
    
 // Contratos - Rutas existentes
    Route::get('contratos', [ContratoController::class, 'index'])->name('contratos.index');
    Route::post('contratos', [ContratoController::class, 'store'])->name('contratos.store');
    Route::get('contratos/{contrato}', [ContratoController::class, 'show'])->name('contratos.show');
    Route::put('contratos/{contrato}', [ContratoController::class, 'update'])->name('contratos.update');
    Route::delete('contratos/{contrato}', [ContratoController::class, 'destroy'])->name('contratos.destroy');
    Route::patch('contratos/{contrato}/toggle-activo', [ContratoController::class, 'toggleActivo'])->name('contratos.toggle-activo');
    
    // ✅ AGREGAR ESTAS RUTAS NUEVAS
    Route::post('contratos/{contrato}/terminar', [ContratoController::class, 'terminar'])->name('contratos.terminar');
    Route::get('contratos-vigentes', [ContratoController::class, 'getVigentes'])->name('contratos.vigentes');
    Route::get('contratos-por-vencer', [ContratoController::class, 'getPorVencer'])->name('contratos.por-vencer');
    Route::get('contratos-personal-disponible', [ContratoController::class, 'getPersonalDisponible'])->name('contratos.personal-disponible');

        //VacacionesNuevo

        Route::get('vacaciones', [VacacionesController::class, 'index'])->name('vacaciones.index');
        Route::post('vacaciones', [VacacionesController::class, 'store'])->name('vacaciones.store');
        Route::get('vacaciones/{vacaciones}', [VacacionesController::class, 'show'])->name('vacaciones.show');
        Route::put('vacaciones/{vacaciones}', [VacacionesController::class, 'update'])->name('vacaciones.update');
        Route::delete('vacaciones/{vacaciones}', [VacacionesController::class, 'destroy'])->name('vacaciones.destroy');
        Route::get('vacaciones/disponibles/{personal}/{anio}', [VacacionesController::class, 'diasDisponiblesPorAnio']);

    });
    
        // ========================================
    // MÓDULO DE ASISTENCIAS
    // ========================================
    Route::prefix('asistencias')->name('asistencias.')->group(function () {
        Route::get('/', [AsistenciaController::class, 'index'])->name('index');
        Route::post('/', [AsistenciaController::class, 'store'])->name('store');
        Route::get('/{asistencia}', [AsistenciaController::class, 'show'])->name('show');
        Route::put('/{asistencia}', [AsistenciaController::class, 'update'])->name('update');
        Route::delete('/{asistencia}', [AsistenciaController::class, 'destroy'])->name('destroy');
    });


    
        Route::get('/turnos', [TurnosController::class, 'index'])->name('turnos.index');
        Route::post('/turnos/store', [TurnosController::class, 'store'])->name('turnos.store');
        Route::get('/turnos/{turno}/show', [TurnosController::class, 'show'])->name('turnos.show'); // Para obtener datos
        Route::put('/turnos/{turno}/update', [TurnosController::class, 'store'])->name('turnos.update'); // El método store maneja update
        Route::delete('/turnos/{turno}/destroy', [TurnosController::class, 'destroy'])->name('turnos.destroy');

        Route::get('/grupospersonal', [GruposPersonalController::class, 'index'])->name('grupospersonal.index');
        Route::post('/grupospersonal/store', [GruposPersonalController::class, 'store'])->name('grupospersonal.store');
        Route::get('/grupospersonal/{grupoPersonal}/show', [GruposPersonalController::class, 'show'])->name('grupospersonal.show');
        Route::put('/grupospersonal/{grupoPersonal}/update', [GruposPersonalController::class, 'update'])->name('grupospersonal.update');
        Route::delete('/grupospersonal/{grupoPersonal}/destroy', [GruposPersonalController::class, 'destroy'])->name('grupospersonal.destroy');
    

// MÓDULO DE PROGRAMACIÓN (Preparado para futuro)
    // ========================================
    

    
            // Rutas de recurso para el CRUD de Programaciones
            Route::get('/programacion', [ProgramacionesController::class, 'index'])->name('programaciones.index');
            Route::post('/programacion/store', [ProgramacionesController::class, 'store'])->name('programaciones.store');
            //Route::get('/programacion/{programacion}/show', [ProgramacionesController::class, 'show'])->name('programaciones.show');
            Route::put('/programacion/{programacion}/update', [ProgramacionesController::class, 'update'])->name('programaciones.update');
            Route::delete('/programacion/{programacion}/destroy', [ProgramacionesController::class, 'destroy'])->name('programaciones.destroy');
            Route::post('/programacion/validar-vacaciones', [ProgramacionesController::class, 'validarVacaciones']);
            Route::post('/programacion/validar-disponibilidad', [ProgramacionesController::class, 'validarDisponibilidadGeneral']);
            // RUTA AJAX CLAVE: Para obtener los detalles del grupo y autocompletar
            Route::get('/programacion/get-grupo-details/{grupoPersonal}', [ProgramacionesController::class, 'getGrupoDetails'])->name('programaciones.get_grupo_details');
// Ruta para actualizar programación con cambios (Avance 04)
Route::put('/programacion/{programacion}/update-con-cambios', [ProgramacionesController::class, 'updateConCambios'])
     ->name('programaciones.update-con-cambios');
     // Ruta para obtener detalle completo de programación (ya existe pero asegúrate de tenerla)
Route::get('/programacion/{programacion}/show', [ProgramacionesController::class, 'show'])
     ->name('programaciones.show');

     
            //Agregué
            Route::post('/programacion/validar-contrato-vigente', [ProgramacionesController::class, 'validarContratoVigente']);
    
            // Ruta para Modificación Masiva
            Route::post('/programacion/modificacion-masiva', [ProgramacionesController::class, 'updateMasiva'])->name('programaciones.update-masiva');
            
            // Ruta de debug para búsqueda
            Route::post('/programacion/debug-busqueda', [ProgramacionesController::class, 'debugBusqueda'])->name('programaciones.debug');
            
            // Rutas para Programación Masiva
            Route::get('/programacion-masiva', [ProgramacionesController::class, 'indexMasiva'])->name('programaciones.masiva');
            Route::post('/programacion-masiva/validar-personal', [ProgramacionesController::class, 'validarDisponibilidadPersonal'])->name('programaciones.masiva.validar');
            Route::post('/programacion-masiva/validar-completa', [ProgramacionesController::class, 'validarProgramacionMasiva'])->name('programaciones.masiva.validar.completa');
            Route::post('/programacion-masiva/store', [ProgramacionesController::class, 'storeMasiva'])->name('programaciones.masiva.store');
            Route::post('/programacion-masiva/validar-disponibilidad', [ProgramacionesController::class, 'validarDisponibilidadVehiculo']);
// Gestión de Cambios - Modificación Masiva
Route::get('/cambios', [ProgramacionesController::class, 'indexCambios'])->name('cambios.index');

    // ✅ NUEVA RUTA - Agregar esta línea
Route::get('/dashboard/programacion/{id}/data', [DashboardController::class, 'getProgramacionData'])
    ->name('dashboard.programacion.data');
    Route::put('/dashboard/programacion/{id}/update-con-cambios', [DashboardController::class, 'updateConCambios'])
    ->name('dashboard.programacion.update-con-cambios');


    Route::prefix('motivos')->group(function () {
    Route::get('/', [MotivoController::class, 'index'])->name('motivos.index');
    Route::post('/', [MotivoController::class, 'store'])->name('motivos.store');
    Route::get('/{motivo}', [MotivoController::class, 'show'])->name('motivos.show');
    Route::put('/{motivo}', [MotivoController::class, 'update'])->name('motivos.update');
    Route::delete('/{motivo}', [MotivoController::class, 'destroy'])->name('motivos.destroy');
    Route::post('/{motivo}/toggle-activo', [MotivoController::class, 'toggleActivo'])->name('motivos.toggle-activo');
});







    // ========================================
    // MÓDULO DE PERSONAL (Preparado para futuro)
    // ========================================
    // Route::prefix('personal')->group(function () {
    //     // Aquí irán las rutas del módulo de personal
    // });

    // ========================================
    // MÓDULO DE ZONAS (Preparado para futuro)
    // ========================================
    // Route::prefix('zonas')->group(function () {
    //     // Aquí irán las rutas del módulo de zonas
    // });

    // ========================================
    // MÓDULO DE PROGRAMACIÓN (Preparado para futuro)
    // ========================================
    // Route::prefix('programacion')->group(function () {
    //     // Aquí irán las rutas del módulo de programación
    // });
});