@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Content Header -->
<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-chart-line text-primary mr-2"></i>Dashboard
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item active">Inicio</li>
                </ol>
            </div>
        </div>
        
        <!-- SELECTOR DE FECHA -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('dashboard') }}" id="formFechaDashboard">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="mb-1"><i class="fas fa-calendar-alt mr-1"></i>Seleccionar Fecha:</label>
                                    <input type="date" 
                                           name="fecha" 
                                           id="fechaDashboard" 
                                           class="form-control" 
                                           value="{{ $fechaSeleccionada->format('Y-m-d') }}"
                                           onchange="this.form.submit()">
                                </div>

                                <div class="col-md-1" >

                                </div>

                                <div class="col-md-4 text-center" >
                                    <div class="btn-group mt-4" role="group">
                                        <button type="button" class="btn btn-outline-primary" onclick="cambiarFecha(-1)">
                                            <i class="fas fa-chevron-left"></i> Día Anterior
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="irHoy()">
                                            <i class="fas fa-calendar-day"></i> Hoy
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" onclick="cambiarFecha(1)">
                                            Día Siguiente <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4 text-right">
                                    <div class="mt-4">
                                        {{-- <strong>Fecha Actual:</strong><br> --}}
                                        <span class="badge badge-info badge-lg p-2" style="font-size: 1rem;">
                                            {{ $fechaSeleccionada->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        
        <!-- ALERTAS CRÍTICAS -->
        @if(!empty($alertasCriticas))
        <div class="row mb-3">
            <div class="col-12">
                @foreach($alertasCriticas as $alerta)
                <div class="alert alert-{{ $alerta['tipo'] }} alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas {{ $alerta['icono'] }} fa-lg mr-3"></i>
                        <div>
                            <strong>{{ $alerta['titulo'] }}</strong>
                            <p class="mb-0">{{ $alerta['mensaje'] }}</p>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- ROW 1: KPIs PRINCIPALES -->
        <div class="row mb-3">

            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box mb-4">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-route"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Programaciones totales</span>
                        <span class="info-box-number">
                            {{ $programacionesTotales }} 
                        </span>
                        
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-primary" style="width: {{ $programacionesTotales > 0 ? round(($programacionesEnProgreso / $programacionesTotales) * 100, 1) : 0 }}%"></div>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-truck text-primary mr-1"></i>
                            <strong class="text-primary">{{ $vehiculosEnRecorrido }}</strong> en recorrido
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4">
                
                <div class="info-box mb-4">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-user-clock"></i></span>
                    <div class="info-box-content" style="height: 100px;">
                        <span class="info-box-text">Asistencias marcadas</span>
                        <span class="info-box-number">{{ $asistenciasRegistradas }} / {{ $asistenciasEsperadas }}</span>
                        
                        @php
                            $tasaAsistencia = $asistenciasEsperadas > 0 ? ($asistenciasRegistradas / $asistenciasEsperadas * 100) : 0;
                            $colorAsistencia = $tasaAsistencia >= 90 ? 'success' : ($tasaAsistencia >= 70 ? 'warning' : 'danger');
                        @endphp
                        
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-{{ $colorAsistencia }}" style="width: {{ $tasaAsistencia }}%"></div>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-times-circle text-danger mr-1"></i>
                            {{ $asistenciasFaltantes }} faltantes
                        </small>
                    </div>
                </div>

                
            </div>

            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box mb-4">
                   <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                    <div class="info-box-content" style="height: 100px;">
                        <span class="info-box-text text-dark">Personal de Apoyo </span>
                        <span class="info-box-number text-info">{{ $totalPersonalLibre ?? 0 }}</span>
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-user-tie text-success mr-1"></i> Conductores: {{ $conductoresLibres ?? 0 }} | Ayudantes: {{ $ayudantesLibres ?? 0 }}
                        </small>
                    </div>
                </div>
            </div>



        </div>


        <!-- ROW 2: ZONAS CON ESTADO Y DISPONIBILIDAD -->
        <div class="row mb-3">
            <!-- ZONAS PROGRAMADAS HOY -->
            <div class="col-md-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-map-marked-alt mr-2"></i>Grupos programados
                        </h3>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto; padding-top: 20px; padding-left:5px; padding-right:8px; padding-bottom:15px;">
                        @if(empty($zonasHoy))
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle mr-2"></i>No hay programaciones para hoy
                        </div>
                        @else
                        <div class="row">
                            @foreach($zonasHoy as $zona)
                            <div class="col-md-4 mb-3">
                                <div class="card @if($zona['puede_iniciar']) border-success @else border-danger @endif">
                                    <div class="card-header @if($zona['puede_iniciar']) bg-success @else bg-danger @endif">
                                        <h5 class="card-title text-white m-0">
                                            @if($zona['puede_iniciar'])
                                                <i class="fas fa-check-circle mr-2"></i>{{ $zona['zona_nombre'] }}
                                            @else
                                                <i class="fas fa-times-circle mr-2"></i>{{ $zona['zona_nombre'] }}
                                            @endif
                                        </h5>
                                    </div>
                                    <div class="card-body p-3">
                                        @foreach($zona['programaciones'] as $prog)
                                        <div class="mb-2 pb-2 border-bottom">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small>
                                                        <strong>{{ $prog['turno'] }}</strong> | 
                                                        <i class="fas fa-car text-info"></i> {{ $prog['vehiculo'] }}
                                                    </small>
                                                    <br>
                                                    <small class="text-muted">
                                                        👤 {{ $prog['conductor'] }}
                                                        @if($prog['ayudantes'] !== 'N/A')
                                                            | 🤝 {{ $prog['ayudantes'] }}
                                                        @endif
                                                    </small>
                                                </div>
                                                <button class="btn btn-sm btn-warning" onclick="editarProgramacionDashboard({{ $prog['id'] }})" title="Editar programación">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach

                                        @if(!$zona['puede_iniciar'])
                                        <div class="alert alert-danger p-2 m-0 mt-2">
                                            <small><strong>Razones:</strong></small>
                                            <ul class="mb-0 pl-3 mt-1" style="font-size: 0.8rem;">
                                                @foreach($zona['razones_bloqueo'] as $razon)
                                                <li>{{ $razon }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @else
                                        <div class="badge badge-success w-100 mt-2 p-2">
                                            <i class="fas fa-check-circle"></i> Listo para iniciar
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- ROW 3: ACCESOS RÁPIDOS -->
        <div class="row mb-3">
            <div class="col-lg-12">
                <div class="card card-outline card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt mr-1"></i>Accesos Rápidos
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-12">
                                <a href="{{ route('vehiculos.vehiculos.index') }}" class="btn btn-app bg-primary w-100">
                                    <i class="fas fa-truck"></i> Ver Vehículos
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <a href="{{ route('personal.personal.index') }}" class="btn btn-app bg-success w-100">
                                    <i class="fas fa-users"></i> Ver Personal
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <a href="{{ route('zonas.index') }}" class="btn btn-app bg-warning w-100">
                                    <i class="fas fa-map-marked-alt"></i> Ver Zonas
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <a href="{{ route('programaciones.index') }}" class="btn btn-app bg-danger w-100">
                                    <i class="fas fa-calendar-alt"></i> Programación
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 4: ALERTAS DE PERSONAL -->
        @if($totalEnVacaciones > 0 || $totalSinContrato > 0)
        <div class="row">
            @if($totalEnVacaciones > 0)
            <div class="col-md-6">
                <div class="card card-outline card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-umbrella-beach mr-2"></i>Personal en Vacaciones Hoy
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($personalConVacacionesHoy as $personal)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $personal['nombre_completo'] ?? $personal->nombre_completo }}</h6>
                                    <small class="text-warning">En vacaciones</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
<!--
            @if($totalSinContrato > 0)
            <div class="col-md-6">
                <div class="card card-outline card-danger">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-contract mr-2"></i>Personal Sin Contrato Vigente
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($personalSinContratoVigente as $personal)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $personal->nombre_completo }}</h6>
                                    <small class="text-danger">Sin contrato</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
-->
        </div>
        @endif

    </div>
</section>

{{-- ========================================
    MODAL: EDITAR PROGRAMACIÓN DESDE DASHBOARD
    ======================================== --}}
<style>
.cambios-section {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #ffc107;
}
.cambios-section h6 {
    color: #495057;
    font-weight: 600;
    margin-bottom: 10px;
}
.cambios-table {
    font-size: 0.9rem;
}
.cambios-table thead {
    background: #e9ecef;
}
</style>

<div class="modal fade" id="modalEditarProgramacionDashboard" tabindex="-1" role="dialog" aria-labelledby="tituloModalEditarDashboard" aria-hidden="true">
 <div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
   <div class="modal-header bg-warning">
    <h5 class="modal-title" id="tituloModalEditarDashboard">
        <i class="fas fa-edit"></i> Editar Programación
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>
   <div class="modal-body">
    <input type="hidden" id="dash_edit_programacion_id">

    {{-- Sección: Cambio de Turno --}}
    <div class="cambios-section mb-3">
     <h6><i class="fas fa-clock mr-2"></i>Cambio de Turno</h6>
     <div class="row align-items-end">
      <div class="col-md-5">
       <label>Turno Actual</label>
       <input type="text" class="form-control" id="dash_edit_turno_actual" readonly>
      </div>
      <div class="col-md-5">
       <label>Nuevo Turno</label>
       <select class="form-control" id="dash_edit_nuevo_turno">
        <option value="">Sin cambio</option>
        @foreach ($turnos ?? [] as $turno)
         <option value="{{ $turno->id }}" data-nombre="{{ $turno->name }}">{{ $turno->name }}</option>
        @endforeach
       </select>
      </div>
      <div class="col-md-2">
       <button type="button" class="btn btn-success btn-block" onclick="dashAgregarCambioTurno()">
        <i class="fas fa-plus"></i>
       </button>
      </div>
     </div>
    </div>

    {{-- Sección: Cambio de Vehículo --}}
    <div class="cambios-section mb-3">
     <h6><i class="fas fa-car mr-2"></i>Cambio de Vehículo</h6>
     <div class="row align-items-end">
      <div class="col-md-5">
       <label>Vehículo Actual</label>
       <input type="text" class="form-control" id="dash_edit_vehiculo_actual" readonly>
      </div>
      <div class="col-md-5">
       <label>Nuevo Vehículo</label>
       <select class="form-control" id="dash_edit_nuevo_vehiculo">
        <option value="">Sin cambio</option>
        @foreach ($vehiculos ?? [] as $vehiculo)
         <option value="{{ $vehiculo->id }}" data-nombre="{{ $vehiculo->codigo }}">{{ $vehiculo->codigo }}</option>
        @endforeach
       </select>
      </div>
      <div class="col-md-2">
       <button type="button" class="btn btn-success btn-block" onclick="dashAgregarCambioVehiculo()">
        <i class="fas fa-plus"></i>
       </button>
      </div>
     </div>
    </div>

    {{-- Sección: Cambio de Personal --}}
    <div class="cambios-section mb-3">
     <h6><i class="fas fa-users mr-2"></i>Cambio de Personal</h6>
     <div class="row align-items-end">
      <div class="col-md-5">
       <label>Personal Actual</label>
       <select class="form-control" id="dash_edit_personal_actual">
        <option value="">Seleccione personal a cambiar</option>
       </select>
      </div>
      <div class="col-md-5">
       <label>Nuevo Personal</label>
       <select class="form-control" id="dash_edit_nuevo_personal">
        <option value="">Seleccione nuevo personal</option>
       </select>
      </div>
      <div class="col-md-2">
       <button type="button" class="btn btn-success btn-block" onclick="dashAgregarCambioPersonal()">
        <i class="fas fa-plus"></i>
       </button>
      </div>
     </div>
    </div>

    {{-- Tabla de Cambios Registrados --}}
    <div class="cambios-section">
     <h6><i class="fas fa-list mr-2"></i>Cambios Registrados</h6>
     <div class="table-responsive">
      <table class="table table-sm table-bordered cambios-table">
       <thead class="thead-light">
        <tr>
         <th width="15%">Tipo</th>
         <th width="20%">Anterior</th>
         <th width="20%">Nuevo</th>
         <th width="15%">Motivo</th>
         <th width="25%">Notas</th>
         <th width="5%">Acción</th>
        </tr>
       </thead>
       <tbody id="dashTablaCambiosRegistrados">
        <tr id="dashNoCambiosRow">
         <td colspan="6" class="text-center text-muted">No hay cambios registrados</td>
        </tr>
       </tbody>
      </table>
     </div>
    </div>

   </div>
   <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-times"></i> Cancelar
    </button>
    <button type="button" class="btn btn-primary" onclick="dashGuardarCambios()">
     <i class="fas fa-save"></i> Guardar Cambios
    </button>
   </div>
  </div>
 </div>
</div>

@endsection

@push('scripts')
<script>
// ==========================================
// VARIABLES GLOBALES DASHBOARD
// ==========================================
let dashCambiosTemp = [];

// ==========================================
// FUNCIÓN: EDITAR PROGRAMACIÓN DESDE DASHBOARD
// ==========================================
window.editarProgramacionDashboard = function(id) {
  dashCambiosTemp = [];
  
  Swal.fire({
    title: 'Cargando...',
    text: 'Obteniendo información...',
    allowOutsideClick: false,
    didOpen: () => { Swal.showLoading(); }
  });

  // ✅ NUEVO: Llamar al endpoint mejorado del controller
  $.get(`/dashboard/programacion/${id}/data`, function(response) {
    Swal.close();
    
    if (response && response.programacion) {
      const prog = response.programacion;
      
      // Establecer datos básicos
      $('#dash_edit_programacion_id').val(prog.id);
      $('#dash_edit_turno_actual').val(prog.turno?.name || 'N/A').data('id', prog.turno_id);
      $('#dash_edit_vehiculo_actual').val(prog.vehiculo?.placa || prog.vehiculo?.codigo || 'N/A').data('id', prog.vehiculo_id);

      // ==========================================
      // ✅ PERSONAL ACTUAL: Solo los que NO marcaron asistencia
      // ==========================================
      let personalHTML = '<option value="">Seleccione personal a cambiar</option>';
      
      if (response.personal_sin_asistencia && response.personal_sin_asistencia.length > 0) {
        response.personal_sin_asistencia.forEach((p) => {
          let rol = p.funcion?.nombre || 'Personal';
          const nombreCompleto = p.nombre_completo || `${p.nombres || ''} ${p.apellidos || ''}`.trim() || 'Sin nombre';
          
          personalHTML += `<option value="${p.id}" data-nombre="${nombreCompleto}" data-funcion="${rol}">
            ${rol}: ${nombreCompleto}
          </option>`;
        });
      } else {
        personalHTML += '<option value="" disabled>Todo el personal marcó asistencia ✓</option>';
      }
      
      $('#dash_edit_personal_actual').html(personalHTML);

      // ==========================================
      // ✅ NUEVO PERSONAL DISPONIBLE: Separado por conductores y ayudantes
      // ==========================================
      let nuevoPersonalHTML = '<option value="">Seleccione nuevo personal</option>';
      
      // Agregar conductores disponibles
      if (response.conductores && response.conductores.length > 0) {
        nuevoPersonalHTML += '<optgroup label="👨‍✈️ Conductores Disponibles">';
        response.conductores.forEach((conductor) => {
          const nombreCompleto = conductor.nombre_completo || `${conductor.nombres || ''} ${conductor.apellidos || ''}`.trim();
          nuevoPersonalHTML += `<option value="${conductor.id}" data-nombre="${nombreCompleto}" data-funcion="Conductor">
            ${nombreCompleto} (Conductor)
          </option>`;
        });
        nuevoPersonalHTML += '</optgroup>';
      }
      
      // Agregar ayudantes disponibles
      if (response.ayudantes && response.ayudantes.length > 0) {
        nuevoPersonalHTML += '<optgroup label="👷 Ayudantes Disponibles">';
        response.ayudantes.forEach((ayudante) => {
          const nombreCompleto = ayudante.nombre_completo || `${ayudante.nombres || ''} ${ayudante.apellidos || ''}`.trim();
          nuevoPersonalHTML += `<option value="${ayudante.id}" data-nombre="${nombreCompleto}" data-funcion="Ayudante">
            ${nombreCompleto} (Ayudante)
          </option>`;
        });
        nuevoPersonalHTML += '</optgroup>';
      }
      
      if (response.conductores.length === 0 && response.ayudantes.length === 0) {
        nuevoPersonalHTML += '<option value="" disabled>No hay personal disponible</option>';
      }
      
      $('#dash_edit_nuevo_personal').html(nuevoPersonalHTML);

      // ==========================================
      // CARGAR TURNOS DISPONIBLES
      // ==========================================
      let turnosHTML = '<option value="">Sin cambio</option>';
      if (response.turnos && response.turnos.length > 0) {
        response.turnos.forEach((turno) => {
          turnosHTML += `<option value="${turno.id}" data-nombre="${turno.name}">${turno.name}</option>`;
        });
      }
      $('#dash_edit_nuevo_turno').html(turnosHTML);

      // ==========================================
      // CARGAR VEHÍCULOS DISPONIBLES
      // ==========================================
      let vehiculosHTML = '<option value="">Sin cambio</option>';
      if (response.vehiculos && response.vehiculos.length > 0) {
        response.vehiculos.forEach((vehiculo) => {
          const identificador = vehiculo.placa || vehiculo.codigo || vehiculo.id;
          vehiculosHTML += `<option value="${vehiculo.id}" data-nombre="${identificador}">${identificador}</option>`;
        });
      }
      $('#dash_edit_nuevo_vehiculo').html(vehiculosHTML);

      // Limpiar tabla de cambios
      $('#dashTablaCambiosRegistrados').html('<tr id="dashNoCambiosRow"><td colspan="6" class="text-center text-muted">No hay cambios registrados</td></tr>');

      // Mostrar modal
      $('#modalEditarProgramacionDashboard').modal('show');
      
      // ✅ Mostrar mensaje informativo si aplica
      if (response.personal_sin_asistencia.length === 0) {
        Swal.fire({
          icon: 'info',
          title: 'Información',
          text: 'Todo el personal asignado ha marcado asistencia correctamente.',
          timer: 3000,
          showConfirmButton: false
        });
      }
      
    } else {
      Swal.fire('Error', 'No se pudo cargar la información de la programación.', 'error');
    }
  }).fail((xhr) => {
    Swal.close();
    console.error('Error al cargar programación:', xhr);
    Swal.fire('Error', 'Fallo de conexión al obtener los datos.', 'error');
  });
};

// ==========================================
// RESTO DE FUNCIONES (sin cambios)
// ==========================================
/*
// AGREGAR CAMBIO: TURNO
window.dashAgregarCambioTurno = function() {
  const nuevoTurnoId = $('#dash_edit_nuevo_turno').val();
  
  if (!nuevoTurnoId) {
    Swal.fire('Atención', 'Debe seleccionar un nuevo turno.', 'warning');
    return;
  }

  const turnoActualId = $('#dash_edit_turno_actual').data('id');
  const turnoActualNombre = $('#dash_edit_turno_actual').val();
  const nuevoTurnoNombre = $('#dash_edit_nuevo_turno option:selected').data('nombre');

  const cambio = {
    tipo_cambio: 'turno',
    valor_anterior: turnoActualId,
    valor_anterior_nombre: turnoActualNombre,
    valor_nuevo: nuevoTurnoId,
    valor_nuevo_nombre: nuevoTurnoNombre,
    motivo_id: null,
    notas: ''
  };

  dashCambiosTemp.push(cambio);
  dashActualizarTablaCambios();
  
  $('#dash_edit_nuevo_turno').val('');
  Swal.fire('Agregado', 'Cambio de turno agregado. No olvide seleccionar un motivo.', 'success');
};

// AGREGAR CAMBIO: VEHÍCULO
window.dashAgregarCambioVehiculo = function() {
  const nuevoVehiculoId = $('#dash_edit_nuevo_vehiculo').val();
  
  if (!nuevoVehiculoId) {
    Swal.fire('Atención', 'Debe seleccionar un nuevo vehículo.', 'warning');
    return;
  }

  const vehiculoActualId = $('#dash_edit_vehiculo_actual').data('id');
  const vehiculoActualNombre = $('#dash_edit_vehiculo_actual').val();
  const nuevoVehiculoNombre = $('#dash_edit_nuevo_vehiculo option:selected').data('nombre');

  const cambio = {
    tipo_cambio: 'vehiculo',
    valor_anterior: vehiculoActualId,
    valor_anterior_nombre: vehiculoActualNombre,
    valor_nuevo: nuevoVehiculoId,
    valor_nuevo_nombre: nuevoVehiculoNombre,
    motivo_id: null,
    notas: ''
  };

  dashCambiosTemp.push(cambio);
  dashActualizarTablaCambios();
  
  $('#dash_edit_nuevo_vehiculo').val('');
  Swal.fire('Agregado', 'Cambio de vehículo agregado. No olvide seleccionar un motivo.', 'success');
};

// AGREGAR CAMBIO: PERSONAL
window.dashAgregarCambioPersonal = function() {
  const personalActualId = $('#dash_edit_personal_actual').val();
  const nuevoPersonalId = $('#dash_edit_nuevo_personal').val();
  
  if (!personalActualId || !nuevoPersonalId) {
    Swal.fire('Atención', 'Debe seleccionar el personal actual y el nuevo.', 'warning');
    return;
  }

  const personalActualNombre = $('#dash_edit_personal_actual option:selected').data('nombre');
  const nuevoPersonalNombre = $('#dash_edit_nuevo_personal option:selected').data('nombre');

  const cambio = {
    tipo_cambio: 'personal',
    valor_anterior: personalActualId,
    valor_anterior_nombre: personalActualNombre,
    valor_nuevo: nuevoPersonalId,
    valor_nuevo_nombre: nuevoPersonalNombre,
    motivo_id: null,
    notas: ''
  };

  dashCambiosTemp.push(cambio);
  dashActualizarTablaCambios();
  
  $('#dash_edit_personal_actual').val('');
  $('#dash_edit_nuevo_personal').val('');
  Swal.fire('Agregado', 'Cambio de personal agregado. No olvide seleccionar un motivo.', 'success');
};

// ACTUALIZAR TABLA DE CAMBIOS
function dashActualizarTablaCambios() {
  const tbody = $('#dashTablaCambiosRegistrados');
  tbody.empty();

  if (dashCambiosTemp.length === 0) {
    tbody.html('<tr id="dashNoCambiosRow"><td colspan="6" class="text-center text-muted">No hay cambios registrados</td></tr>');
    return;
  }

  dashCambiosTemp.forEach((c, idx) => {
    let tipoLabel = '';
    if (c.tipo_cambio === 'turno') tipoLabel = 'Turno';
    else if (c.tipo_cambio === 'vehiculo') tipoLabel = 'Vehículo';
    else if (c.tipo_cambio === 'personal') tipoLabel = 'Personal';

    const row = `
      <tr>
        <td>${tipoLabel}</td>
        <td>${c.valor_anterior_nombre}</td>
        <td>${c.valor_nuevo_nombre}</td>
        <td>
          <select class="form-control form-control-sm" onchange="dashCambiosTemp[${idx}].motivo_id = this.value">
            <option value="">Seleccione</option>
            @foreach ($motivos ?? [] as $motivo)
              <option value="{{ $motivo->id }}" ${c.motivo_id == {{ $motivo->id }} ? 'selected' : ''}>{{ $motivo->descripcion }}</option>
            @endforeach
          </select>
        </td>
        <td>
          <input type="text" class="form-control form-control-sm" placeholder="Notas (opcional)" 
                 value="${c.notas || ''}" 
                 onchange="dashCambiosTemp[${idx}].notas = this.value">
        </td>
        <td>
          <button class="btn btn-danger btn-sm" onclick="dashEliminarCambio(${idx})">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      </tr>
    `;
    tbody.append(row);
  });
}

// ELIMINAR CAMBIO
window.dashEliminarCambio = function(index) {
  dashCambiosTemp.splice(index, 1);
  dashActualizarTablaCambios();
  Swal.fire('Eliminado', 'Cambio eliminado de la lista.', 'info');
};

// GUARDAR CAMBIOS
window.dashGuardarCambios = function() {
  if (dashCambiosTemp.length === 0) {
    Swal.fire('Atención', 'No hay cambios para guardar.', 'warning');
    return;
  }

  // Validar que todos los cambios tengan motivo
  const sinMotivo = dashCambiosTemp.filter(c => !c.motivo_id);
  if (sinMotivo.length > 0) {
    Swal.fire('Atención', 'Todos los cambios deben tener un motivo seleccionado.', 'warning');
    return;
  }

  const programacionId = $('#dash_edit_programacion_id').val();

  Swal.fire({
    title: '¿Confirmar cambios?',
    text: `Se registrarán ${dashCambiosTemp.length} cambio(s) en la programación.`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, guardar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: 'Guardando...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
      });

      $.ajax({
        url: `/programacion/${programacionId}/cambios`,
        method: 'POST',
        data: {
          _token: '{{ csrf_token() }}',
          cambios: dashCambiosTemp
        },
        success: function(response) {
          Swal.close();
          if (response.success) {
            Swal.fire('Guardado', response.message || 'Cambios guardados correctamente.', 'success')
              .then(() => {
                $('#modalEditarProgramacionDashboard').modal('hide');
                location.reload();
              });
          } else {
            Swal.fire('Error', response.message || 'No se pudieron guardar los cambios.', 'error');
          }
        },
        error: function(xhr) {
          Swal.close();
          console.error('Error al guardar:', xhr);
          Swal.fire('Error', 'Ocurrió un error al guardar los cambios.', 'error');
        }
      });
    }
  });
};
*/



//==========================================
// AGREGAR CAMBIO: TURNO
// ==========================================
window.dashAgregarCambioTurno = function() {
  const nuevoTurnoId = $('#dash_edit_nuevo_turno').val();
  
  if (!nuevoTurnoId) {
    Swal.fire('Atención', 'Debe seleccionar un nuevo turno.', 'warning');
    return;
  }

  const turnoActualId = $('#dash_edit_turno_actual').data('id');
  const turnoActualNombre = $('#dash_edit_turno_actual').val();
  const nuevoTurnoNombre = $('#dash_edit_nuevo_turno option:selected').data('nombre');

  const cambio = {
    tipo_cambio: 'turno',
    valor_anterior: turnoActualId,
    valor_anterior_nombre: turnoActualNombre,
    valor_nuevo: nuevoTurnoId,
    valor_nuevo_nombre: nuevoTurnoNombre,
    motivo_id: null,
    notas: ''
  };

  dashCambiosTemp.push(cambio);
  dashActualizarTablaCambios();
  
  $('#dash_edit_nuevo_turno').val('');
  Swal.fire('Agregado', 'Cambio de turno agregado. No olvide seleccionar un motivo.', 'success');
};

// ==========================================
// AGREGAR CAMBIO: VEHÍCULO
// ==========================================
window.dashAgregarCambioVehiculo = function() {
  const nuevoVehiculoId = $('#dash_edit_nuevo_vehiculo').val();
  
  if (!nuevoVehiculoId) {
    Swal.fire('Atención', 'Debe seleccionar un nuevo vehículo.', 'warning');
    return;
  }

  const vehiculoActualId = $('#dash_edit_vehiculo_actual').data('id');
  const vehiculoActualNombre = $('#dash_edit_vehiculo_actual').val();
  const nuevoVehiculoNombre = $('#dash_edit_nuevo_vehiculo option:selected').data('nombre');

  const cambio = {
    tipo_cambio: 'vehiculo',
    valor_anterior: vehiculoActualId,
    valor_anterior_nombre: vehiculoActualNombre,
    valor_nuevo: nuevoVehiculoId,
    valor_nuevo_nombre: nuevoVehiculoNombre,
    motivo_id: null,
    notas: ''
  };

  dashCambiosTemp.push(cambio);
  dashActualizarTablaCambios();
  
  $('#dash_edit_nuevo_vehiculo').val('');
  Swal.fire('Agregado', 'Cambio de vehículo agregado. No olvide seleccionar un motivo.', 'success');
};

// ==========================================
// AGREGAR CAMBIO: PERSONAL
// ==========================================
window.dashAgregarCambioPersonal = function() {
  const personalActualId = $('#dash_edit_personal_actual').val();
  const nuevoPersonalId = $('#dash_edit_nuevo_personal').val();
  
  if (!personalActualId || !nuevoPersonalId) {
    Swal.fire('Atención', 'Debe seleccionar el personal actual y el nuevo.', 'warning');
    return;
  }

  const personalActualNombre = $('#dash_edit_personal_actual option:selected').data('nombre');
  const nuevoPersonalNombre = $('#dash_edit_nuevo_personal option:selected').data('nombre');

  const cambio = {
    tipo_cambio: 'personal',
    valor_anterior: personalActualId,
    valor_anterior_nombre: personalActualNombre,
    valor_nuevo: nuevoPersonalId,
    valor_nuevo_nombre: nuevoPersonalNombre,
    motivo_id: null,
    notas: ''
  };

  dashCambiosTemp.push(cambio);
  dashActualizarTablaCambios();
  
  $('#dash_edit_personal_actual').val('');
  $('#dash_edit_nuevo_personal').val('');
  Swal.fire('Agregado', 'Cambio de personal agregado. No olvide seleccionar un motivo.', 'success');
};

// ==========================================
// ACTUALIZAR TABLA DE CAMBIOS
// ==========================================
function dashActualizarTablaCambios() {
  const tbody = $('#dashTablaCambiosRegistrados');
  tbody.empty();

  if (dashCambiosTemp.length === 0) {
    tbody.html('<tr id="dashNoCambiosRow"><td colspan="6" class="text-center text-muted">No hay cambios registrados</td></tr>');
    return;
  }

  dashCambiosTemp.forEach((c, idx) => {
    let tipoLabel = '';
    if (c.tipo_cambio === 'turno') tipoLabel = 'Turno';
    else if (c.tipo_cambio === 'vehiculo') tipoLabel = 'Vehículo';
    else if (c.tipo_cambio === 'personal') tipoLabel = 'Personal';

    const row = `
      <tr>
        <td>${tipoLabel}</td>
        <td>${c.valor_anterior_nombre}</td>
        <td>${c.valor_nuevo_nombre}</td>
        <td>
          <select class="form-control form-control-sm" onchange="dashCambiosTemp[${idx}].motivo_id = this.value">
            <option value="">Seleccione</option>
            @foreach ($motivos ?? [] as $motivo)
              <option value="{{ $motivo->id }}" ${c.motivo_id == {{ $motivo->id }} ? 'selected' : ''}>{{ $motivo->descripcion }}</option>
            @endforeach
          </select>
        </td>
        <td>
          <input type="text" class="form-control form-control-sm" placeholder="Notas (opcional)" 
                 value="${c.notas || ''}" 
                 onchange="dashCambiosTemp[${idx}].notas = this.value">
        </td>
        <td>
          <button class="btn btn-danger btn-sm" onclick="dashEliminarCambio(${idx})">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      </tr>
    `;
    tbody.append(row);
  });
}


// ==========================================
// ELIMINAR CAMBIO
// ==========================================

window.dashEliminarCambio = function(index) {
  dashCambiosTemp.splice(index, 1);
  dashActualizarTablaCambios();
  Swal.fire('Eliminado', 'Cambio eliminado de la lista.', 'info');
};

// ==========================================
// GUARDAR CAMBIOS
// ==========================================
window.dashGuardarCambios = function() {
  if (dashCambiosTemp.length === 0) {
    Swal.fire('Atención', 'No hay cambios para guardar.', 'warning');
    return;
  }

  // Validar que todos los cambios tengan motivo
  const sinMotivo = dashCambiosTemp.filter(c => !c.motivo_id);
  if (sinMotivo.length > 0) {
    Swal.fire('Atención', 'Todos los cambios deben tener un motivo seleccionado.', 'warning');
    return;
  }

  const programacionId = $('#dash_edit_programacion_id').val();

  Swal.fire({
    title: '¿Guardar cambios?',
    text: `Se registrarán ${dashCambiosTemp.length} cambio(s) en esta programación.`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, guardar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: 'Guardando...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
      });

      // Preparar cambios de personal si existen
      let personalChanges = [];
      dashCambiosTemp.forEach(c => {
        if (c.tipo_cambio === 'personal') {
          personalChanges.push({
            anterior_id: c.valor_anterior,
            nuevo_id: c.valor_nuevo
          });
        }
      });

      // Extraer turno_id y vehiculo_id de los cambios
      let turnoId = null;
      let vehiculoId = null;
      
      dashCambiosTemp.forEach(c => {
        if (c.tipo_cambio === 'turno') {
          turnoId = c.valor_nuevo;
        } else if (c.tipo_cambio === 'vehiculo') {
          vehiculoId = c.valor_nuevo;
        }
      });

      $.ajax({
        url: `/dashboard/programacion/${programacionId}/update-con-cambios`,
        type: 'PUT',
        data: {
          _token: '{{ csrf_token() }}',
          cambios: dashCambiosTemp,
          personal_changes: personalChanges.length > 0 ? personalChanges : null,
          turno_id: turnoId,
          vehiculo_id: vehiculoId
        },
        success: function(res) {
          Swal.close();
          if (res.success) {
            Swal.fire('Éxito', res.message || 'Cambios guardados correctamente.', 'success').then(() => {
              $('#modalEditarProgramacionDashboard').modal('hide');
              location.reload(); // Recargar dashboard para ver cambios
            });
          } else {
            Swal.fire('Error', res.message || 'No se pudieron guardar los cambios.', 'error');
          }
        },
        error: function(xhr) {
          Swal.close();
          const msg = xhr.responseJSON?.message || 'Error en la conexión.';
          Swal.fire('Error', msg, 'error');
        }
      });
    }
  });
};

// ==========================================
// NAVEGACIÓN DE FECHAS EN DASHBOARD
// ==========================================
function cambiarFecha(dias) {
    const inputFecha = document.getElementById('fechaDashboard');
    const fechaActual = new Date(inputFecha.value);
    fechaActual.setDate(fechaActual.getDate() + dias);
    
    const año = fechaActual.getFullYear();
    const mes = String(fechaActual.getMonth() + 1).padStart(2, '0');
    const dia = String(fechaActual.getDate()).padStart(2, '0');
    
    inputFecha.value = `${año}-${mes}-${dia}`;
    document.getElementById('formFechaDashboard').submit();
}

function irHoy() {
    const hoy = new Date();
    const año = hoy.getFullYear();
    const mes = String(hoy.getMonth() + 1).padStart(2, '0');
    const dia = String(hoy.getDate()).padStart(2, '0');
    
    document.getElementById('fechaDashboard').value = `${año}-${mes}-${dia}`;
    document.getElementById('formFechaDashboard').submit();
}

// Atajos de teclado
document.addEventListener('keydown', function(e) {
    // Alt + Left Arrow = Día anterior
    if (e.altKey && e.key === 'ArrowLeft') {
        e.preventDefault();
        cambiarFecha(-1);
    }
    // Alt + Right Arrow = Día siguiente
    if (e.altKey && e.key === 'ArrowRight') {
        e.preventDefault();
        cambiarFecha(1);
    }
    // Alt + T = Hoy
    if (e.altKey && e.key === 't') {
        e.preventDefault();
        irHoy();
    }
});
</script>
@endpush