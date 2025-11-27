@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

<style>
/* Estilos base (Manteniendo tus estilos originales) */
.badge-aprobado { background-color: #28a745; color: #fff; }
.badge-cancelado { background-color: #dc3545; color: #fff; }
.badge-secondary { background-color: #6c757d; color: #fff; }
.badge-warning { background-color: #ffc107; color: #000; }

.modal-header {
 background-color: #3f6791;
 color: #fff;
 border-top-left-radius: .3rem;
 border-top-right-radius: .3rem;
}
.modal-header .close span {
 color: #fff !important;
}

.card-group-details {
 background-color: #f8f9fa; 
 border-left: 5px solid #007bff;
 padding: 15px;
 margin-top: 15px;
 border-radius: 5px;
}
.card-group-details p {
 margin-bottom: 5px;
}
.card-group-details strong {
 color: #3f6791;
}

.select-locked {
  background-color: #e9ecef !important;
  cursor: default !important; 
}

/* Estilos para tabla de cambios */
.cambios-table {
    font-size: 0.9rem;
}

.cambios-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.cambios-section {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 15px;
}

.cambios-section h6 {
    color: #3f6791;
    font-weight: bold;
    margin-bottom: 10px;
}

/* Botón de agregar cambio */
.btn-add-change {
    width: 40px;
    height: 40px;
    padding: 0;
    border-radius: 50%;
}

/* Input de notas en tabla */
.nota-input {
    width: 100%;
    min-height: 60px;
    resize: vertical;
}

/* Estilos personalizados para SweetAlert modales de modificación masiva */
.swal2-wide {
    width: auto !important;
    max-width: 600px !important;
}

.swal2-html-container {
    padding: 20px !important;
}

/* --- ESTILOS ESPECÍFICOS DEL CALENDARIO DE CONFLICTOS --- */
#calendar-conflicts .fc-event-title-container {
    text-align: center;
    font-weight: bold;
    color: white; 
}

#calendar-conflicts {
    visibility: hidden; 
    min-height: 250px; 
}

#conflict-calendar-card .card-header {
    background-color: #dc3545; /* Fondo rojo de alerta */
    color: white;
    padding: 0; 
}
#conflict-calendar-card .card-header a {
    color: white;
    text-decoration: none;
    display: block; 
    padding: 1rem 1.25rem; 
}

/* --- ESTILO DE LOS BOTONES DE NAVEGACIÓN (PREV/NEXT) --- */

/* Aplica el estilo al botón contenedor */
.fc-prev-button,
.fc-next-button {
    /* Fondo blanco */
    background-color: #ffffff !important; 
    /* Borde negro claro (opcional, pero se ve mejor con fondo blanco) */
    border-color: #ffffff !important;    /* Asegurar el color del texto (aunque solo se vea el icono) */
    color: #000000 !important; 
    /* Eliminar cualquier sombra que ponga Bootstrap o FullCalendar */
    box-shadow: none !important;
}

/* Aplica el color negro al icono (flecha) dentro del botón */
.fc-prev-button .fc-icon,
.fc-next-button .fc-icon {
    color: #000000 !important;
}

/* --- ESTILOS PARA EL ICONO DESPLEGABLE --- */


/* Fuerza la capitalización, color negro y tamaño 18px en los nombres de los días de la semana */
.fc-col-header-cell-cushion {
    text-transform: capitalize !important;
    color: #000000 !important; /* 🔥 Color Negro 🔥 */
    font-size: 16px !important; /* 🔥 Tamaño 18px 🔥 */
    font-weight: bold !important; /* Opcional, pero ayuda a que resalten */
}

/* Asegura que el texto y el icono se muestren correctamente */
#conflict-calendar-header a {
    text-decoration: none; /* Quita el subrayado del enlace */
    color: inherit; /* Mantiene el color del texto del encabezado de la tarjeta */
}

/* Transición para la rotación del icono */
.toggle-icon {
    transition: transform 0.3s ease;
}

/* Rota el icono cuando el enlace NO tiene la clase 'collapsed' (es decir, cuando está expandido) */
#conflict-calendar-header a:not(.collapsed) .toggle-icon {
    transform: rotate(180deg);
}

/* Alineación del texto dentro del card-title */
#conflict-calendar-header .card-title {
    width: 100%;
}

/* Centrar spinner */
#calendar-loading {
     min-height: 250px;
     display: flex;
     flex-direction: column;
     justify-content: center;
     align-items: center;
}

/* ======================================================= */
/* --- CORRECCIONES CLAVE PARA VISUALIZACIÓN DEL CALENDARIO --- */
/* ======================================================= */

/* 1. ELIMINAR EL FONDO GRIS Y LA ATENUACIÓN (OPACIDAD) EN DÍAS DE OTROS MESES */

/* A. Eliminar el fondo de la celda (td) */
/* Uso de 'background' en lugar de 'background-color' para mayor prioridad */
td.fc-daygrid-day.fc-day-other {
    background: #fff !important; /* Fuerza el color blanco */
    opacity: 1 !important; /* Desactiva la opacidad de la celda */
}

/* B. Eliminar la opacidad en los números de día y texto */
/* Esto anula la opacidad que hace que el texto parezca gris */
.fc-day-other {
    opacity: 1 !important; /* Fuerza la opacidad 1 al contenedor principal */
}

/* C. Asegurar el color del texto del número de día */
.fc-day-other .fc-daygrid-day-top a {
    color: #343a40 !important; /* Color de texto estándar */
    opacity: 1 !important; 
}

/* 2. ELIMINAR EL RESALTADO AMARILLO DEL DÍA ACTUAL (fc-day-today) */
/* Apunta a la celda del día (td) cuando es el día actual */
.fc-daygrid-day.fc-day-today {
    background-color: #fff !important; /* Fondo blanco */
    border-color: #dee2e6 !important; /* Borde estándar para evitar el azul/amarillo */
}
/* Asegura que el número del día actual no esté en negrita ni resaltado */
.fc-daygrid-day.fc-day-today .fc-daygrid-day-top a {
    font-weight: normal !important; 
    color: #343a40 !important; 
}

/* Ajuste general para los números de día */
.fc-daygrid-day-number {
    color: #343a40 !important;
}

.fc-toolbar-title {
    text-transform: capitalize !important;
    font-size: 18px !important; /* Agregado el tamaño de 16px */
    font-weight: bold !important; /* 🔥 ESTO PONE EL TEXTO EN NEGRITA 🔥 */
}

</style>
@endpush


@section('content')
<div class="content-header">
 <div class="container-fluid">
  <div class="row mb-2">
   <div class="col-sm-6">
    <h1 class="m-0">Gestión de Programaciones</h1>
   </div>
   <div class="col-sm-6">
    <ol class="breadcrumb float-sm-right">
     <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
     <li class="breadcrumb-item active">Programaciones</li>
    </ol>
   </div>
  </div>
 </div>
</div>

<div class="content">
 <div class="container-fluid">
  <div class="card">
   <div class="card-header">
    <h3 class="card-title">Listado de Programaciones</h3>
    <div class="card-tools">
     <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
      <i class="fas fa-calendar-alt"></i> Nueva Programación
     </button>
     <button type="button" class="btn btn-info btn-sm" id="btnModificacionMasiva">
      <i class="fas fa-exchange-alt"></i> Modificación Masiva
     </button>
     <button type="button" 
        class="btn btn-secondary btn-sm {{ request()->routeIs('programaciones.masiva') ? 'active' : '' }}"
        onclick="window.location='{{ route('programaciones.masiva') }}'">
        <i class="fas fa-fw fa-layer-group nav-icon"></i> Programación Masiva
     </button>
    </div>
   </div>
   <div class="card-body">

     <div class="row mb-3 justify-content-center"> 

    <div class="col-md-3 text-center">
        <label for="fecha_inicio">Fecha de Inicio</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control">
    </div>

    <div class="col-md-3 text-center">
        <label for="fecha_fin">Fecha de Fin</label>
        <input type="date" id="fecha_fin" name="fecha_fin" class="form-control">
    </div>

    <div class="col-md-1">
        <label class="invisible d-block">.</label> 
        <button id="btnFiltrar" class="btn btn-primary w-100" title="Aplicar Filtro">
            <i class="fas fa-filter"></i> 
        </button>
    </div>

    <div class="col-md-1">
        <label class="invisible d-block">.</label> 
        <button id="btnLimpiar" class="btn btn-secondary w-100" title="Mostrar todos los registros">
            <i class="fas fa-eraser"></i>
        </button>
    </div>

    <div class="col-md-1">
        <label class="invisible d-block">.</label> 
        <button id="btnHoy" class="btn btn-success w-100" title="Ver registros de hoy">
            <i class="fas fa-calendar-day"></i> 
        </button>
    </div>
</div>

    <div id="cargando-container" style="text-align: center; padding: 50px;">
            <div class="spinner-border text-primary" role="status">
            </div>
        </div>

    <div id="tabla-container" style="display: none;">

    <table id="tablaProgramaciones" class="table table-bordered table-striped table-hover">
     <thead>
      <tr>
       <th>Grupo</th>
       <th>Fecha</th>
       <th>Turno</th>
       <th>Zona</th>
       <th>Vehículo</th>
       <th>Estado</th>
       <th>Acciones</th>
      </tr>
     </thead>
     <tbody id="tablaProgramacionesBody">
       @foreach ($programaciones ?? [] as $p)
       <tr id="row_{{ $p->id }}">
        <td>{{ $p->grupo->nombre ?? 'N/A' }}</td>
        <td>{{ optional($p->fecha_inicio)->format('Y-m-d') }}</td>
        <td>{{ $p->turno->name ?? 'N/A' }}</td>
        <td>{{ $p->zona->nombre ?? 'N/A' }}</td>
        <td>{{ $p->vehiculo->codigo ?? 'N/A' }}</td>
        <td>
         <span class="badge {{ $p->statusBadge }}">{{ $p->statusLabel }}</span>
        </td>
        <td>
         <div class="btn-group">
           <button class="btn btn-warning btn-sm" onclick="verDetalle({{ $p->id }})" title="Ver Detalle">
             <i class="fas fa-eye"></i>
           </button>
           <button class="btn btn-info btn-sm" onclick="editarProgramacion({{ $p->id }})" title="Editar">
             <i class="fas fa-edit"></i>
           </button>
           <button class="btn btn-danger btn-sm btnEliminar" data-id="{{ $p->id }}" title="Eliminar">
             <i class="fas fa-ban"></i>
           </button>
         </div>
        </td>
       </tr>
       @endforeach
     </tbody>
    </table>

    </div>
   </div>
  </div>
 </div>
</div>

{{-- ========================================
    MODAL: NUEVA PROGRAMACIÓN (YA EXISTENTE)
    ======================================== --}}
<div class="modal fade" id="modalProgramacion" tabindex="-1" role="dialog" aria-labelledby="tituloModalProgramacion" aria-hidden="true">
 <div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">
   <div class="modal-header">
    <h5 class="modal-title" id="tituloModalProgramacion">Registrar Programación</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
     <span aria-hidden="true">&times;</span>
    </button>
    </div>
   <form id="formProgramacion">
    @csrf
    <div class="modal-body">
                    <input type="hidden" name="programacion_id" id="programacion_id">

                    {{-- Fechas --}}
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="fecha_fin" class="form-label">Fecha de Fin <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                            </div>
                        </div>
                    </div>

                    {{-- Grupo - Turno, Vehículo y Zona (Bloqueados por el Grupo) --}}
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label for="grupo_id" class="form-label">Grupo de Personal <span class="text-danger">*</span></label>
                            <select class="form-control" id="grupo_id" name="grupo_id" style="width: 100%;" required>
                                <option value="">Seleccione un Grupo</option>
                                @foreach ($grupos ?? [] as $grupo)
                                    <option value="{{ $grupo->id }}" data-turno="{{ $grupo->turno_id }}" data-vehiculo="{{ $grupo->vehiculo_id }}" data-zona="{{ $grupo->zona_id }}" data-dias="{{ $grupo->dias ?? '' }}" data-conductor="{{ $grupo->conductor_id ?? '' }}" data-ayudante1="{{ $grupo->ayudante1_id ?? '' }}" data-ayudante2="{{ $grupo->ayudante2_id ?? '' }}">{{ $grupo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 form-group">
                            <label for="turno_id" class="form-label">Turno <span class="text-danger">*</span></label>
                            <select class="form-control select-locked" id="turno_id" name="turno_id" style="width: 100%;" required data-locked="true">
                                <option value="">Seleccione un Turno</option>
                                @foreach ($turnos ?? [] as $turno)
                                    <option value="{{ $turno->id }}">{{ $turno->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 form-group">
                            <label for="vehiculo_id" class="form-label">Vehículo <span class="text-danger">*</span></label>
                            <select class="form-control select-locked" id="vehiculo_id" name="vehiculo_id" style="width: 100%;" required data-locked="true">
                                <option value="">Seleccione un Vehículo</option>
                                @foreach ($vehiculos ?? [] as $vehiculo)
                                    <option value="{{ $vehiculo->id }}">{{ $vehiculo->codigo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 form-group">
                            <label for="zona_id" class="form-label">Zona <span class="text-danger">*</span></label>
                            <select class="form-control select-locked" id="zona_id" name="zona_id" style="width: 100%;" required data-locked="true">
                                <option value="">Seleccione una Zona</option>
                                @foreach ($zonas ?? [] as $zona)
                                    <option value="{{ $zona->id }}">{{ $zona->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Días de Trabajo (Checkboxes) --}}
                    <div class="form-group">
                        <label class="form-label">Días de trabajo <span class="text-danger">*</span></label>
                        <div class="row" id="dias-trabajo-checkboxes">
                            <div class="col-auto"> <div class="form-check form-check-inline"> <input class="form-check-input" type="checkbox" id="dia_lunes" name="dias[]" value="Lunes"> <label class="form-check-label" for="dia_lunes">Lunes</label> </div> </div>
                            <div class="col-auto"> <div class="form-check form-check-inline"> <input class="form-check-input" type="checkbox" id="dia_martes" name="dias[]" value="Martes"> <label class="form-check-label" for="dia_martes">Martes</label> </div> </div>
                            <div class="col-auto"> <div class="form-check form-check-inline"> <input class="form-check-input" type="checkbox" id="dia_miercoles" name="dias[]" value="Miércoles"> <label class="form-check-label" for="dia_miercoles">Miércoles</label> </div> </div>
                            <div class="col-auto"> <div class="form-check form-check-inline"> <input class="form-check-input" type="checkbox" id="dia_jueves" name="dias[]" value="Jueves"> <label class="form-check-label" for="dia_jueves">Jueves</label> </div> </div>
                            <div class="col-auto"> <div class="form-check form-check-inline"> <input class="form-check-input" type="checkbox" id="dia_viernes" name="dias[]" value="Viernes"> <label class="form-check-label" for="dia_viernes">Viernes</label> </div> </div>
                            <div class="col-auto"> <div class="form-check form-check-inline"> <input class="form-check-input" type="checkbox" id="dia_sabado" name="dias[]" value="Sábado"> <label class="form-check-label" for="dia_sabado">Sábado</label> </div> </div>
                        </div>
                    </div>

        {{-- INICIO: Contenedor del Calendario de Conflictos (El que solicitaste) --}}
        <div id="conflict-calendar-card" class="mt-3 card card-danger card-outline" style="display: none;">
            <div class="card-header p-0" id="conflict-calendar-header" >
                {{-- Enlace que actúa como toggle --}}
                <a class="d-block w-100 text-left collapsed" data-toggle="collapse" href="#calendar-collapse-body" role="button" aria-expanded="false" aria-controls="calendar-collapse-body" style="padding-bottom: 16px; ">
        <p class="card-title mb-0">
            <div class="d-flex justify-content-between align-items-center">
                {{-- Título principal (eliminado el "(clic)") --}}
                <span class="mr-3"> <strong> ⚠️ Superposición de programación: </strong> Existen programaciones para el mismo turno, zona y vehículo en el rango de fechas. </span>
                
                {{-- 🔥 ICONO DESPLEGABLE (Font Awesome) 🔥 --}}
                {{-- El estado del icono cambia automáticamente con el collapse de Bootstrap --}}
                <i class="fas fa-chevron-down toggle-icon"></i>
            </div>
        </p>
    </a>
            </div>
            {{-- Cuerpo colapsable que contiene el calendario --}}
            <div id="calendar-collapse-body" class="collapse" aria-labelledby="conflict-calendar-header">
                <div class="card-body">
                    {{-- Spinner de Carga (Oculto inicialmente por JS) --}}
                    <div id="calendar-loading" class="text-center p-4" style="display: none;">
                        <div class="spinner-border text-danger" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                    {{-- Calendario (Contenedor de FullCalendar) --}}
                    <div id="calendar-conflicts"></div>
                </div>
            </div>
        </div>
        {{-- FIN Contenedor del Calendario de Conflictos --}}
                    {{-- Mensajes de Validación General --}}
                    <div id="general-validation-messages" class="mt-3"></div>

                    {{-- Personal --}}
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="conductor_id" class="form-label">Conductor <span class="text-danger">*</span> </label>
                            <select class="form-control" id="conductor_id" name="conductor_id" style="width: 100%;" required>
                                <option value="">Seleccione un Conductor</option>
                                @foreach ($conductores ?? [] as $c)
                                    <option value="{{ $c->id }}">{{ $c->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 form-group">
                            <label for="ayudante1_id" class="form-label">Ayudante 1 <span class="text-danger">*</span> </label>
                            <select class="form-control" id="ayudante1_id" name="ayudante1_id" style="width: 100%;" required>
                                <option value="">Seleccione un Ayudante</option>
                                @foreach ($ayudantes ?? [] as $a)
                                    <option value="{{ $a->id }}">{{ $a->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 form-group">
                            <label for="ayudante2_id" class="form-label">Ayudante 2 <span class="text-danger">*</span> </label>
                            <select class="form-control " id="ayudante2_id" name="ayudante2_id" style="width: 100%;" required>
                                <option value="">Seleccione un Ayudante</option>
                                @foreach ($ayudantes ?? [] as $a)
                                    <option value="{{ $a->id }}">{{ $a->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Mensajes de Validación de Personal (Vacaciones y Contrato no Vigente) --}}
                    <div id="vacaciones-validation-messages" class="mt-3"></div>

                    {{-- Estado (Solo visible en Edición) --}}
                    <div class="row mt-3">
                        <div class="col-md-4 form-group" id="status-group" style="display: none;">
                            <label for="status" class="form-label">Estado</label>
                            <select class="form-control" id="status" name="status">
                                <option value="1">Programada</option>
                                <option value="0">Cancelada</option>
                            </select>
                        </div>
                    </div>

                </div> 

    <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
     <button type="submit" class="btn btn-success" id="btnGuardar">Guardar Programación</button>
    </div>
   </form>
  </div>
 </div>
</div>

{{-- ========================================
    MODAL: EDITAR PROGRAMACIÓN (AVANCE 04)
    ======================================== --}}
<div class="modal fade" id="modalEditarProgramacion" tabindex="-1" role="dialog" aria-labelledby="tituloModalEditar" aria-hidden="true">
 <div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
   <div class="modal-header">
    <h5 class="modal-title" id="tituloModalEditar">Editar Programación</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>
   <div class="modal-body">
    <input type="hidden" id="edit_programacion_id">

    {{-- Sección: Cambio de Turno --}}
    <div class="cambios-section">
     <h6>Cambio de Turno</h6>
     <div class="row align-items-end">
      <div class="col-md-5">
       <label>Turno Actual</label>
       <input type="text" class="form-control" id="edit_turno_actual" readonly>
      </div>
      <div class="col-md-5">
       <label>Nuevo Turno</label>
       <select class="form-control" id="edit_nuevo_turno">
        <option value="">Sin cambio</option>
        @foreach ($turnos ?? [] as $turno)
         <option value="{{ $turno->id }}" data-nombre="{{ $turno->name }}">{{ $turno->name }}</option>
        @endforeach
       </select>
      </div>
      <div class="col-md-2">
       <button type="button" class="btn btn-success btn-add-change" onclick="agregarCambioTurno()">
        <i class="fas fa-plus"></i>
       </button>
      </div>
     </div>
    </div>

    {{-- Sección: Cambio de Vehículo --}}
    <div class="cambios-section">
     <h6>Cambio de Vehículo</h6>
     <div class="row align-items-end">
      <div class="col-md-5">
       <label>Vehículo Actual</label>
       <input type="text" class="form-control" id="edit_vehiculo_actual" readonly>
      </div>
      <div class="col-md-5">
       <label>Nuevo Vehículo</label>
       <select class="form-control" id="edit_nuevo_vehiculo">
        <option value="">Sin cambio</option>
        @foreach ($vehiculos ?? [] as $vehiculo)
         <option value="{{ $vehiculo->id }}" data-nombre="{{ $vehiculo->codigo }}">{{ $vehiculo->codigo }}</option>
        @endforeach
       </select>
      </div>
      <div class="col-md-2">
       <button type="button" class="btn btn-success btn-add-change" onclick="agregarCambioVehiculo()">
        <i class="fas fa-plus"></i>
       </button>
      </div>
     </div>
    </div>

    {{-- Sección: Cambio de Personal --}}
    <div class="cambios-section">
     <h6>Cambio de Personal</h6>
     <div class="row align-items-end">
      <div class="col-md-5">
       <label>Personal Actual</label>
       <select class="form-control" id="edit_personal_actual">
        <option value="">Seleccione personal a cambiar</option>
       </select>
      </div>
      <div class="col-md-5">
       <label>Nuevo Personal</label>
       <select class="form-control" id="edit_nuevo_personal">
        <option value="">Seleccione nuevo personal</option>
       </select>
      </div>
      <div class="col-md-2">
       <button type="button" class="btn btn-success btn-add-change" onclick="agregarCambioPersonal()">
        <i class="fas fa-plus"></i>
       </button>
      </div>
     </div>
    </div>

    {{-- Tabla de Cambios Registrados --}}
    <div class="cambios-section">
     <h6>Cambios Registrados</h6>
     <table class="table table-sm table-bordered cambios-table">
      <thead>
       <tr>
        <th width="15%">Tipo de Cambio</th>
        <th width="20%">Valor Anterior</th>
        <th width="20%">Valor Nuevo</th>
        <th width="15%">Motivo</th>
        <th width="25%">Notas</th>
        <th width="5%">Acción</th>
       </tr>
      </thead>
      <tbody id="tablaCambiosRegistrados">
       <tr id="noCambiosRow">
        <td colspan="6" class="text-center text-muted">No hay cambios registrados</td>
       </tr>
      </tbody>
     </table>
    </div>

   </div>
   <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
    <button type="button" class="btn btn-primary" onclick="guardarCambios()">
     <i class="fas fa-save"></i> Guardar Cambios
    </button>
   </div>
  </div>
 </div>
</div>

{{-- ========================================
    MODAL: VER DETALLE (AVANCE 04)
    ======================================== --}}
<div class="modal fade" id="modalVerDetalle" tabindex="-1" role="dialog" aria-labelledby="tituloModalDetalle" aria-hidden="true">
 <div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
   <div class="modal-header">
    <h5 class="modal-title" id="tituloModalDetalle">Visualización de día programado e historial de cambios</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>
   <div class="modal-body">
    
    {{-- Datos Generales --}}
    <div class="card mb-3">
     <div class="card-header bg-light">
      <h6 class="mb-0"><i class="fas fa-info-circle"></i> Datos Generales</h6>
     </div>
     <div class="card-body">
      <table class="table table-sm table-borderless">
       <tr>
        <th width="20%">Fecha:</th>
        <td id="detalle_fecha"></td>
        <th width="20%">Estado:</th>
        <td id="detalle_estado"></td>
       </tr>
       <tr>
        <th>Zona:</th>
        <td id="detalle_zona"></td>
        <th>Turno:</th>
        <td id="detalle_turno"></td>
       </tr>
       <tr>
        <th>Vehículo:</th>
        <td colspan="3" id="detalle_vehiculo"></td>
       </tr>
      </table>
     </div>
    </div>

    {{-- Personal Asignado --}}
    <div class="card mb-3">
     <div class="card-header bg-light">
      <h6 class="mb-0"><i class="fas fa-users"></i> Personal Asignado</h6>
     </div>
     <div class="card-body">
      <table class="table table-sm table-bordered">
       <thead>
        <tr>
         <th width="30%">Rol</th>
         <th>Nombre</th>
        </tr>
       </thead>
       <tbody id="detallePersonalBody">
        <tr>
         <td colspan="2" class="text-center">Cargando...</td>
        </tr>
       </tbody>
      </table>
     </div>
    </div>

    {{-- Historial de Cambios --}}
    <div class="card">
     <div class="card-header bg-light">
      <h6 class="mb-0"><i class="fas fa-history"></i> Historial de Cambios</h6>
     </div>
     <div class="card-body">
      <table class="table table-sm table-bordered">
       <thead>
        <tr>
         <th>Fecha del Cambio</th>
         <th>Valor Anterior</th>
         <th>Valor Nuevo</th>
         <th>Motivo</th>
        </tr>
       </thead>
       <tbody id="detalleHistorialBody">
        <tr>
         <td colspan="4" class="text-center text-muted">No hay cambios registrados</td>
        </tr>
       </tbody>
      </table>
     </div>
    </div>

   </div>
   <div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">
     <i class="fas fa-times"></i> Cerrar
    </button>
   </div>
  </div>
 </div>
</div>

{{-- ========================================
    MODAL: MODIFICACIÓN MASIVA
    ======================================== --}}
<div class="modal fade" id="modalModificacionMasiva" tabindex="-1" role="dialog" aria-labelledby="tituloModalModificacionMasiva" aria-hidden="true">
 <div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
   <div class="modal-header">
    <h5 class="modal-title" id="tituloModalModificacionMasiva">
     <i class="fas fa-exchange-alt"></i> Cambio Masivo
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>
   <form id="formModificacionMasiva">
    @csrf
    <div class="modal-body">
     
     {{-- Fecha de Inicio --}}
     <div class="row">
      <div class="col-md-6 form-group">
       <label for="masiva_fecha_inicio" class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
       <input type="date" class="form-control" id="masiva_fecha_inicio" name="fecha_inicio" required>
      </div>
      
      {{-- Fecha de Fin --}}
      <div class="col-md-6 form-group">
       <label for="masiva_fecha_fin" class="form-label">Fecha de Fin <span class="text-danger">*</span></label>
       <input type="date" class="form-control" id="masiva_fecha_fin" name="fecha_fin" required>
      </div>
     </div>

     {{-- Zona (Opcional) --}}
     <div class="form-group">
      <label for="masiva_zona_id" class="form-label">Zonas (Opcional) <small class="text-muted">Dejar vacío para aplicar a todas las zonas</small></label>
      <select class="form-control" id="masiva_zona_id" name="zona_id" style="width: 100%;">
       <option value="">Seleccione zonas (opcional)</option>
       @foreach ($zonas ?? [] as $zona)
        <option value="{{ $zona->id }}">{{ $zona->nombre }}</option>
       @endforeach
      </select>
     </div>

     {{-- Tipo de Cambio --}}
     <div class="form-group">
      <label for="masiva_tipo_cambio" class="form-label">Tipo de Cambio <span class="text-danger">*</span></label>
      <select class="form-control" id="masiva_tipo_cambio" name="tipo_cambio" required>
       <option value="">Seleccione tipo de cambio</option>
       <option value="conductor">Cambio de Conductor</option>
       <option value="ocupante">Cambio de Ocupante</option>
       <option value="turno">Cambio de Turno</option>
       <option value="vehiculo">Cambio de Vehículo</option>
      </select>
     </div>

     {{-- Campos dinámicos según tipo de cambio --}}
     <div id="cambio_conductor_fields" style="display: none;">
      <div class="form-group">
       <label for="masiva_conductor_nuevo" class="form-label">Nuevo Conductor <span class="text-danger">*</span></label>
       <small class="text-muted d-block mb-2">El sistema buscará y modificará todas las programaciones del rango de fechas, reemplazando cualquier conductor por el seleccionado.</small>
       <select class="form-control" id="masiva_conductor_nuevo" name="conductor_nuevo">
        <option value="">Seleccione nuevo conductor</option>
        @foreach ($conductores ?? [] as $c)
         <option value="{{ $c->id }}">{{ $c->nombre_completo }}</option>
        @endforeach
       </select>
      </div>
     </div>

     <div id="cambio_ocupante_fields" style="display: none;">
      <div class="form-group">
       <label for="masiva_ocupante_nuevo" class="form-label">Nuevo Ocupante <span class="text-danger">*</span></label>
       <small class="text-muted d-block mb-2">El sistema buscará y modificará todas las programaciones del rango de fechas, reemplazando cualquier ocupante por el seleccionado.</small>
       <select class="form-control" id="masiva_ocupante_nuevo" name="ocupante_nuevo">
        <option value="">Seleccione nuevo ocupante</option>
        @foreach ($ayudantes ?? [] as $a)
         <option value="{{ $a->id }}">{{ $a->nombre_completo }}</option>
        @endforeach
       </select>
      </div>
     </div>

     <div id="cambio_turno_fields" style="display: none;">
      <div class="form-group">
       <label for="masiva_turno_nuevo" class="form-label">Nuevo Turno <span class="text-danger">*</span></label>
       <small class="text-muted d-block mb-2">El sistema buscará y modificará todas las programaciones del rango de fechas, reemplazando cualquier turno por el seleccionado.</small>
       <select class="form-control" id="masiva_turno_nuevo" name="turno_nuevo">
        <option value="">Seleccione nuevo turno</option>
        @foreach ($turnos ?? [] as $turno)
         <option value="{{ $turno->id }}">{{ $turno->name }}</option>
        @endforeach
       </select>
      </div>
     </div>

     <div id="cambio_vehiculo_fields" style="display: none;">
      <div class="form-group">
       <label for="masiva_vehiculo_nuevo" class="form-label">Nuevo Vehículo <span class="text-danger">*</span></label>
       <small class="text-muted d-block mb-2">El sistema buscará y modificará todas las programaciones del rango de fechas, reemplazando cualquier vehículo por el seleccionado.</small>
       <select class="form-control" id="masiva_vehiculo_nuevo" name="vehiculo_nuevo">
        <option value="">Seleccione nuevo vehículo</option>
        @foreach ($vehiculos ?? [] as $v)
         <option value="{{ $v->id }}">{{ $v->codigo }}</option>
        @endforeach
       </select>
      </div>
     </div>

     {{-- Motivo --}}
     <div class="form-group">
      <label for="masiva_motivo_id" class="form-label">Motivo <span class="text-danger">*</span></label>
      <select class="form-control" id="masiva_motivo_id" name="motivo_id" required>
       <option value="">Seleccione motivo</option>
       @foreach ($motivos ?? [] as $motivo)
        <option value="{{ $motivo->id }}">{{ $motivo->nombre }}</option>
       @endforeach
      </select>
     </div>

     {{-- Notas --}}
     <div class="form-group">
      <label for="masiva_notas" class="form-label">Notas (Opcional)</label>
      <textarea class="form-control" id="masiva_notas" name="notas" rows="3" placeholder="Ingrese notas adicionales..."></textarea>
     </div>

     {{-- Mensajes de validación --}}
     <div id="masiva-validation-messages" class="mt-3"></div>

    </div>

    <div class="modal-footer">
     <button type="button" class="btn btn-warning btn-sm" onclick="debugBusquedaMasiva()" title="Verificar qué programaciones se encontrarían">
      <i class="fas fa-bug"></i> Debug
     </button>
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
     <button type="submit" class="btn btn-success" id="btnGuardarModificacionMasiva">
      <i class="fas fa-save"></i> Guardar Cambios Masivos
     </button>
    </div>
   </form>
  </div>
 </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js'></script>

<script>
  $(document).ready(function() {

      const COLUMNA_FECHA = 1; 

    // --- Funciones Auxiliares ---

    // Obtener la fecha de hoy en formato YYYY-MM-DD
    function getTodayDate() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Maneja la visibilidad de la tabla y el spinner
    function toggleLoading(isLoading) {
        if (isLoading) {
            $('#cargando-container').show();
            $('#tabla-container').hide();
        } else {
            $('#cargando-container').hide();
            $('#tabla-container').show();
        }
    }

    // Aplica el filtro de la tabla
    function applyFilter(table) {
        toggleLoading(true); // Mostrar cargando
        
        // El draw() llama a la función de filtro personalizada
        table.draw(); 
        
        // Ocultar el cargando una vez que la tabla se redibuja
        // Usamos un setTimeout leve, ya que en Client-Side es casi instantáneo
        setTimeout(() => {
            toggleLoading(false);
        }, 100); 
    }

    // --- 1. FUNCIÓN DE FILTRADO PERSONALIZADA (Client-Side) ---

    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'tablaProgramaciones') { return true; }
            
            const fechaInicioStr = $('#fecha_inicio').val();
            const fechaFinStr = $('#fecha_fin').val();
            const fechaRegistroStr = data[COLUMNA_FECHA]; 
            
            // Si las fechas están vacías, mostrar todos los registros (Comportamiento "Limpiar")
            if (!fechaInicioStr || !fechaFinStr) { 
                return true; 
            }

            // Convertir fechas a entero para comparación YYYYMMDD
            const min = parseInt(fechaInicioStr.replace(/-/g, ''), 10);
            const max = parseInt(fechaFinStr.replace(/-/g, ''), 10);
            const fecha = parseInt(fechaRegistroStr.replace(/-/g, ''), 10);

            return (fecha >= min && fecha <= max);
        }
    );

    // --- 2. INICIALIZACIÓN DE DATATABLES ---

    // Establecer la fecha de hoy por defecto ANTES de inicializar DataTables
    const today = getTodayDate();
    $('#fecha_inicio').val(today);
    $('#fecha_fin').val(today);
    
    const table = $('#tablaProgramaciones').DataTable({
        language: {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron programaciones",
            "sEmptyTable":     "No hay programaciones",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
            "sInfoFiltered":   "",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        order: [[COLUMNA_FECHA, 'asc']],
        
        // CALLBACK: Muestra la tabla después de aplicar el filtro inicial
        initComplete: function(settings, json) {
            // La tabla se inicializa y se dibuja, aplicando el filtro de 'hoy' por defecto.
            toggleLoading(false);
        }
    });

    // --- 3. MANEJO DE BOTONES ---

    // Botón Filtrar (ya existía)
    $('#btnFiltrar').on('click', function() {
        applyFilter(table);
    });
    
    // Botón Limpiar: Vacía los campos de fecha y recarga la tabla
    $('#btnLimpiar').on('click', function() {
        $('#fecha_inicio').val('');
        $('#fecha_fin').val('');
        applyFilter(table);
    });

    // Botón Hoy: Establece las fechas de hoy y recarga la tabla
    $('#btnHoy').on('click', function() {
        const today = getTodayDate();
        $('#fecha_inicio').val(today);
        $('#fecha_fin').val(today);
        applyFilter(table);
    });
    
  // ==========================================
  // BLOQUEAR SELECTORES LOCKED
  // ==========================================
  $('[data-locked="true"]').each(function() {
    const $select = $(this);

    $select.on('mousedown', function(e) {
      e.preventDefault();
      this.blur();
      window.focus();
    });

    $select.on('keydown', function(e) {
      e.preventDefault();
      return false;
    });
  });

  // ==========================================
  // BOTÓN NUEVA PROGRAMACIÓN
  // ==========================================
  $('#btnNuevo').click(function() {
    resetFormulario();
    $('#tituloModalProgramacion').text('Registrar Programación');
    $('#btnGuardar').text('Guardar Programación').removeClass('btn-info').addClass('btn-success');
    $('#modalProgramacion').modal('show');
  });

  // ==========================================
  // RESETEAR FORMULARIO
  // ==========================================
  function resetFormulario() {
    $('#formProgramacion')[0].reset();
    $('#programacion_id').val('');
    $('input[name="dias[]"]').prop('checked', false);
    $('#grupo_id, #turno_id, #vehiculo_id, #zona_id, #conductor_id, #ayudante1_id, #ayudante2_id').val('').trigger('change');
    
    // Limpiar mensajes de validación
    $('#general-validation-messages').empty();
    $('#vacaciones-validation-messages').empty();
    
    // Limpiar restricción de fecha mínima
    $('#fecha_fin').removeAttr('min');
  }

  // ==========================================
  // CARGAR DATOS DEL GRUPO
  // ==========================================
  function cargarDatosGrupo(grupoId) {
    if (!grupoId) {
      $('#turno_id, #vehiculo_id, #zona_id, #conductor_id, #ayudante1_id, #ayudante2_id').val('');
      $('input[name="dias[]"]').prop('checked', false);
      return;
    }

    const $option = $('#grupo_id option:selected');
    const turnoId = $option.data('turno');
    const vehiculoId = $option.data('vehiculo');
    const zonaId = $option.data('zona');
    const conductorId = $option.data('conductor');
    const ayudante1Id = $option.data('ayudante1');
    const ayudante2Id = $option.data('ayudante2');
    const dias = $option.data('dias');

    if (turnoId) $('#turno_id').val(turnoId);
    if (vehiculoId) $('#vehiculo_id').val(vehiculoId);
    if (zonaId) $('#zona_id').val(zonaId);
    if (conductorId) $('#conductor_id').val(conductorId);
    if (ayudante1Id) $('#ayudante1_id').val(ayudante1Id);
    if (ayudante2Id) $('#ayudante2_id').val(ayudante2Id);

    $('input[name="dias[]"]').prop('checked', false);
    if (dias) {
      const diasArr = dias.split(',');
      diasArr.forEach(dia => {
        $(`input[value="${dia.trim()}"]`).prop('checked', true);
      });
    }
  }
  // RESTRICCIÓN DE FECHA FIN
    // ==========================================
    $('#fecha_inicio').on('change', function() {
      const fechaInicio = $(this).val();
      const $fechaFin = $('#fecha_fin');
      $fechaFin.attr('min', fechaInicio);
      if ($fechaFin.val() && $fechaFin.val() < fechaInicio) {
        $fechaFin.val(fechaInicio);
      }
    });


  // Carga de datos del grupo y limpieza de mensajes
  $('#grupo_id').on('change', function() {
    cargarDatosGrupo($(this).val());
    $('#general-validation-messages').empty();
    $('#vacaciones-validation-messages').empty();
  });

  // ==========================================
  // VALIDAR VACACIONES
  // ==========================================
  async function validarVacaciones(formData) {
    const dataToCheck = {
      _token: '{{ csrf_token() }}',
      conductor_id: formData.find(item => item.name === 'conductor_id')?.value,
      ayudante1_id: formData.find(item => item.name === 'ayudante1_id')?.value,
      ayudante2_id: formData.find(item => item.name === 'ayudante2_id')?.value,
      fecha_inicio: formData.find(item => item.name === 'fecha_inicio')?.value,
      fecha_fin: formData.find(item => item.name === 'fecha_fin')?.value,
    };
    
    const endpoint = '/programacion/validar-vacaciones'; 

    try {
      const response = await $.ajax({
        url: endpoint,
        type: 'POST',
        data: dataToCheck,
      });

      if (response.success === false) {
        let conflictMsg = 'El siguiente personal tiene vacaciones programadas en este periodo:<br><ul>';
        response.conflicts.forEach(c => {
          conflictMsg += `<li><strong>${c.rol} (${c.nombre}):</strong> ${c.fechas}</li>`;
        });
        conflictMsg += '</ul>';

        return { isValid: false, message: conflictMsg };
      }
      return { isValid: true };

    } catch (xhr) {
      let errorMsg = 'Error al verificar la disponibilidad de vacaciones.';
      if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
        errorMsg = 'Complete todos los campos requeridos.';
      } else if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMsg = xhr.responseJSON.message;
      }
      return { isValid: false, message: errorMsg };
    }
  }
  // ==========================================
  // VALIDAR VIGENCIA DE CONTRATO
  // ==========================================
  async function validarVigenciaContrato(formData) {
    const dataToCheck = {
      _token: '{{ csrf_token() }}',
      conductor_id: formData.find(item => item.name === 'conductor_id')?.value || 0,
      ayudante1_id: formData.find(item => item.name === 'ayudante1_id')?.value || 0,
      ayudante2_id: formData.find(item => item.name === 'ayudante2_id')?.value || 0,
      fecha_inicio: formData.find(item => item.name === 'fecha_inicio')?.value,
      fecha_fin: formData.find(item => item.name === 'fecha_fin')?.value,
    };

    const endpoint = '/programacion/validar-contrato-vigente';

    try {
      const response = await $.ajax({
        url: endpoint,
        type: 'POST',
        data: dataToCheck,
      });

      if (response.success === false) {
        let conflictMsg = 'El siguiente personal tiene contrato no vigente en el periodo de programación:<br><ul>';
        response.conflicts.forEach(c => {
          conflictMsg += `<li><strong>${c.rol} (${c.nombre}):</strong> ${c.detalle}</li>`;
        });
        conflictMsg += '</ul>';
        return { isValid: false, message: conflictMsg };
      }
      return { isValid: true };

    } catch (xhr) {
      let errorMsg = 'Error al verificar la vigencia del contrato.';
      if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
        errorMsg = 'Complete todos los campos requeridos para la validación.';
      } else if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMsg = xhr.responseJSON.message;
      }
      return { isValid: false, message: `Fallo de validación de contrato: ${errorMsg}` };
    }
  }


  

  // ==========================================
  // VCALENDARIO

    // Función auxiliar para obtener el formato YYYY-MM-DD del texto (Mantenida)
    const getRawDate = (dayString) => {
        const match = dayString.match(/\((.*?)\)/);
        return match ? match[1] : null; // Devuelve solo 'YYYY-MM-DD'
    };
    
    // Función de formato de fecha auxiliar (Mantenida)
    const formatDate = (date) => {
        const d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        return [year, month.padStart(2, '0'), day.padStart(2, '0')].join('-');
    };

    /**
     * Inicializa y renderiza FullCalendar con las fechas de conflicto y de programación.
     * @param {string[]} conflictDates - Fechas bloqueadas (rojo).
     * @param {string[]} completedDates - Fechas a programar (turquesa).
     * @param {string} fechaInicio - Fecha de inicio para centrar la vista.
     */
    function renderConflictCalendar(conflictDates, completedDates, fechaInicio) {
        const calendarEl = document.getElementById('calendar-conflicts');
        
        // Ocultar el spinner y el calendario antes de renderizar (Mantenido)
        $('#calendar-conflicts').css('visibility', 'hidden'); 
        $('#calendar-loading').hide();
        
        // Crear eventos (Mantenido)
        const conflictEvents = conflictDates.map(date => ({
            title: '📅  Programado',
            start: date, 
            allDay: true,
            backgroundColor: '#fb7005ff', 
            borderColor: '#fb7005ff',
        }));
        
        const completedEvents = completedDates.map(date => ({
            title: '✅ Sin programar',
            start: date, 
            allDay: true,
            backgroundColor: '#17a2b8', 
            borderColor: '#17a2b8'
        }));
        
        const events = [...conflictEvents, ...completedEvents];
        
        // --- LÓGICA DE NAVEGACIÓN (MANTENIDA) ---
        const allDates = [...conflictDates, ...completedDates].filter(d => d).sort();
        if (allDates.length === 0) return; 

        const firstDateStr = allDates[0]; 
        const lastDateStr = allDates[allDates.length - 1]; 
        
        const firstDateObj = new Date(firstDateStr + 'T00:00:00');
        const validStartMonth = new Date(firstDateObj.getFullYear(), firstDateObj.getMonth(), 1);
        const validStart = formatDate(validStartMonth); 

        const lastDateObj = new Date(lastDateStr + 'T00:00:00'); 
        const validEndMonth = new Date(lastDateObj.getFullYear(), lastDateObj.getMonth() + 1, 1);
        const validEnd = formatDate(validEndMonth);
        
        // --- FIN LÓGICA DE NAVEGACIÓN ---

        // Destruir instancia anterior si existe
        if (calendarEl.calendar) {
            calendarEl.calendar.destroy();
        }
        
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth', 
            locale: 'es', 
            height: 'auto',
            fixedWeekCount: false, 
            showNonCurrentDates: false, 
            
            // Volvemos al formato de objeto que no da error, aunque ponga "de"
            titleFormat: { 
                month: 'long', 
                year: 'numeric' 
            }, 
            
            validRange: {
                start: validStart, 
                end: validEnd 
            },
            headerToolbar: {
                left: 'prev',         // Botón Anterior a la izquierda
                center: 'title',      // Título al centro
                right: 'next'         // Botón Siguiente a la derecha
            },
            events: events,
            eventContent: function(arg) {
                return { html: `<div class="fc-event-title-container">${arg.event.title}</div>` };
            }
        });
        
        calendar.render(); 
        calendarEl.calendar = calendar; 

        // Mostrar la tarjeta de conflictos (Mantenido)
        $('#conflict-calendar-card').show(); 
        
        // Manejo del colapso (Mantenido)
        $('#calendar-collapse-body').off('show.bs.collapse').on('show.bs.collapse', function () {
            $('#calendar-loading').show();
        });

        $('#calendar-collapse-body').off('shown.bs.collapse').on('shown.bs.collapse', function () {
            
            calendar.updateSize(); 
            const earliestDate = allDates.length > 0 ? allDates[0] : fechaInicio;
            calendar.gotoDate(earliestDate);
            
            // ----------------------------------------------------------------------------------
            
            $('#calendar-loading').hide();
            $('#calendar-conflicts').css('visibility', 'visible'); 
            
        }).off('hidden.bs.collapse').on('hidden.bs.collapse', function() {
            $('#calendar-conflicts').css('visibility', 'hidden'); 
            $('#calendar-loading').hide();
        });
    }

    // Ocultar el calendario al resetear el modal (Mantenido)
    $('#modalProgramacion').on('hidden.bs.modal', function () {
        $('#conflict-calendar-card').hide(); 
        $('#general-validation-messages').empty();
        $('#calendar-collapse-body').collapse('hide'); 
        $('#calendar-conflicts').css('visibility', 'hidden'); 
        $('#calendar-loading').hide();
    });
  
  
  
  // VALIDAR DISPONIBILIDAD GENERAL
  // ==========================================



  async function validarDisponibilidadGeneral(formData) {
        
        // 1. LIMPIZA Y REINICIO (MANTENIDO)
        $('#general-validation-messages').empty();
        $('#conflict-calendar-card').hide(); 
        $('#calendar-conflicts').css('visibility', 'hidden'); 
        $('#calendar-loading').hide();
        // Aseguramos que el botón esté habilitado por defecto hasta que se detecte un bloqueo
        
        $('#btnGuardar').prop('disabled', false); 

        const diasSeleccionados = formData.filter(item => item.name === 'dias[]').map(item => item.value);

        const dataToCheck = {
            _token: '{{ csrf_token() }}',
            programacion_id: formData.find(item => item.name === 'programacion_id')?.value || null,
            conductor_id: formData.find(item => item.name === 'conductor_id')?.value,
            ayudante1_id: formData.find(item => item.name === 'ayudante1_id')?.value || 0,
            ayudante2_id: formData.find(item => item.name === 'ayudante2_id')?.value || 0,
            vehiculo_id: formData.find(item => item.name === 'vehiculo_id')?.value,
            grupo_id: formData.find(item => item.name === 'grupo_id')?.value,
            zona_id: formData.find(item => item.name === 'zona_id')?.value,
            fecha_inicio: formData.find(item => item.name === 'fecha_inicio')?.value,
            fecha_fin: formData.find(item => item.name === 'fecha_fin')?.value,
            dias: diasSeleccionados,
        };

        const endpoint = '/programacion/validar-disponibilidad';
        let htmlContent = '';
        let message = 'La programación es válida.';

        let hasPartialSuggestion = false;
        let fullyBlockedDaysCount = 0;

        // Función auxiliar para formatear fechas (MANTENIDA)
        const formatDateForDisplay = (dayString) => {
            const match = dayString.match(/\((.*?)\)/);
            if (match && match[1]) {
                const [year, month, day] = match[1].split('-');
                const dayName = dayString.split(' ')[0];
                return `${dayName} (${day}/${month}/${year})`;
            }
            return dayString;
        };
        
        // Nota: La función getRawDate debe estar definida en el Bloque A del calendario.

        try {
            const response = await $.ajax({
                url: endpoint,
                type: 'POST',
                data: dataToCheck,
            });

            const conflicts = response.conflicts || {};
            let availableDaysSuggestion = response.available_days_suggestion || [];

            const rutaConflicto = conflicts.superposicion_ruta && conflicts.superposicion_ruta.dates && conflicts.superposicion_ruta.dates.length > 0;

            if (rutaConflicto) {

                // ----------------------------------------------------------------------
                // INYECCIÓN DE LÓGICA DE CALENDARIO (CLAVE) - MANTENIDA
                // ----------------------------------------------------------------------
                const rawConflictDates = conflicts.superposicion_ruta.dates.map(getRawDate).filter(d => d);
                const rawCompletedDates = availableDaysSuggestion.map(getRawDate).filter(d => d);
                const fechaInicio = dataToCheck.fecha_inicio;
                
                // 2. Renderizar el calendario con AMBOS conjuntos de fechas
                if ((rawConflictDates.length > 0 || rawCompletedDates.length > 0) && fechaInicio) {
                    // renderConflictCalendar debe existir y estar definido
                    renderConflictCalendar(rawConflictDates, rawCompletedDates, fechaInicio); 
                }
                // ----------------------------------------------------------------------

                // ----------------------------------------------------------------------
                // GENERACIÓN DE ALERTA DE CONFLICTO (LIMPIADA)
                // ----------------------------------------------------------------------
                message = response.message || 'Se encontró Superposición de Programación.';
                
                // ----------------------------------------------------------------------

                // Identificar días totalmente bloqueados (MANTENIDO Y NECESARIO)
                const conflictDayNames = [...new Set(conflicts.superposicion_ruta.dates.map(d => d.split(' ')[0]))];
                const suggestedDaysInConflictWeekdays = availableDaysSuggestion.filter(d => {
                    const dayName = d.split(' ')[0];
                    return conflictDayNames.includes(dayName);
                });

                const suggestedDayNames = [...new Set(suggestedDaysInConflictWeekdays.map(d => d.split(' ')[0]))];
                const fullyBlockedDays = conflictDayNames.filter(dayName => !suggestedDayNames.includes(dayName));
                fullyBlockedDaysCount = fullyBlockedDays.length;

                let suggestionMessageHtml = '';
                
                // El bloque de sugerencia parcial (availableDaysSuggestion) fue eliminado.

                  
                // Sugerencia parcial
                if (suggestedDaysInConflictWeekdays.length > 0) {
                  hasPartialSuggestion = true;
                  const formattedAvailableDays = suggestedDaysInConflictWeekdays.map(d => formatDateForDisplay(d));

                }



                // Bloqueo total (MANTENIDO)
                if (fullyBlockedDaysCount > 0) {
                    const diasParaDesmarcar = fullyBlockedDays.join(', ');
                    const blockMessage = `El día ${diasParaDesmarcar} ya no tiene disponibilidad para programar en este rango de fechas.`;

                    suggestionMessageHtml += '<div class="alert" style="padding:0px; background-color: white">' +
                        `<p class="mb-1" style="color: red; padding-left: -2px; font-size: 14px"> ${blockMessage}</p>` +
                        '</div>';
                    
                    // 4. DESHABILITAR GUARDAR SI HAY BLOQUEO TOTAL
                    // $('#btnGuardar').prop('disabled', true);
                }

                htmlContent += '<div>' + suggestionMessageHtml + '</div>';
                
                // 5. INYECTAR CONTENIDO HTML
                $('#general-validation-messages').html(htmlContent);

                return {
                    isValid: false, 
                    message: message,
                    html: htmlContent,
                    hasPartialSuggestion: hasPartialSuggestion, 
                    fullyBlockedDaysCount: fullyBlockedDaysCount,
                    available_days_suggestion: availableDaysSuggestion
                };

            } else {
                // Caso sin conflicto (MANTENIDO)
                message = response.message || 'La programación es válida.';
                htmlContent += '<div class="alert alert-success">';
                htmlContent += '<strong>✅ Programación Válida.</strong>';
                htmlContent += '<p class="mt-2"></p>';
                htmlContent += '</div>';

                // Asegurar que la tarjeta de conflicto esté oculta y botón habilitado (MANTENIDO)
                $('#conflict-calendar-card').hide(); 
                $('#btnGuardar').prop('disabled', false); 

                // **LISTA DE FECHAS DISPONIBLES ELIMINADA (Caso sin conflicto)**

                $('#general-validation-messages').html(htmlContent);

                return {
                    isValid: true,
                    message: message,
                    html: htmlContent,
                    hasPartialSuggestion: false,
                    fullyBlockedDaysCount: 0,
                    available_days_suggestion: availableDaysSuggestion
                };
            }

        } catch (xhr) {
            // Manejo de error (MANTENIDO)
             $('#conflict-calendar-card').hide(); 
             $('#btnGuardar').prop('disabled', true); 
             
            let errorMsg = 'Error desconocido al validar la disponibilidad.';
            if (xhr.status === 422) {
                errorMsg = xhr.responseJSON.message || 'Datos incompletos o inválidos.';
            } else if (xhr.status === 404) {
                 errorMsg = xhr.responseJSON.message || 'Endpoint no encontrado.';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                 errorMsg = xhr.responseJSON.message;
            }
            
            message = 'Fallo de conexión o error de servidor.';
            htmlContent = `<div class="alert alert-danger"><strong>🚨 Error:</strong> ${errorMsg}</div>`;
            
            $('#general-validation-messages').html(htmlContent);
            
            return {
                isValid: false,
                message: message,
                html: htmlContent,
                hasPartialSuggestion: false,
                fullyBlockedDaysCount: 1, 
                available_days_suggestion: []
            };
        }
    }



 
 
 
 
  // SUBMIT FORMULARIO (CREAR PROGRAMACIÓN)
 
 
 
  // ==========================================
  // ==========================================
  // SUBMIT FORMULARIO (CREAR/ACTUALIZAR PROGRAMACIÓN)
  // ==========================================
  $('#formProgramacion').submit(async function(e) {
    e.preventDefault();

    let formData = $(this).serializeArray();
    const $form = $(this);

    // Limpiar mensajes previos
    $('#general-validation-messages').empty();
    $('#vacaciones-validation-messages').empty();

    // Validación básica
    if (!$form[0].reportValidity() || $('input[name="dias[]"]:checked').length === 0) {
      Swal.fire('Atención', 'Por favor complete todos los campos requeridos y seleccione al menos un día de trabajo.', 'info');
      return;
    }

    // Validar personal único
    if (!validarPersonalUnico()) {
      return;
    }

    Swal.fire({
      title: 'Validando Programación',
      text: 'Validando disponibilidad, contratos y conflictos de ruta...',
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });

    // Validaciones en paralelo
    const [validationVacacionesResult, validationContratoResult] = await Promise.all([
      validarVacaciones(formData),
      validarVigenciaContrato(formData)
    ]);

    let hayConflictoVacaciones = !validationVacacionesResult.isValid;
    let hayConflictoContrato = !validationContratoResult.isValid;
    let personalValidationHtml = '';
    let finalMessage = '';
    
    if (hayConflictoVacaciones) {
      personalValidationHtml += `<div class="alert alert-danger mb-3"><strong>⚠️ Conflicto de Vacaciones:</strong> ${validationVacacionesResult.message}</div>`;
      finalMessage += 'Se encontraron conflictos de vacaciones.';
    }

    if (hayConflictoContrato) {
      personalValidationHtml += `<div class="alert alert-danger mb-3"><strong>⚠️ Contratos con conflicto:</strong> ${validationContratoResult.message}</div>`;
      finalMessage += (finalMessage ? ' y ' : '') + 'Se encontraron contratos no vigentes.';
    }
    
    $('#vacaciones-validation-messages').html(personalValidationHtml);

    // Validación general
    const generalValidationResult = await validarDisponibilidadGeneral(formData);
    let hayConflictoGeneral = !generalValidationResult.isValid;

    $('#general-validation-messages').html(generalValidationResult.html);

    if (hayConflictoGeneral) {
      finalMessage += (finalMessage ? ' y ' : '') + 'Se encontraron conflictos de programación general.';
    }

    const id = $('#programacion_id').val();
    const url = id ? `/programacion/${id}/update` : `/programacion/store`;
    const method = id ? 'PUT' : 'POST';

    // A. Conflictos Bloqueantes (Vacaciones o Contratos)
    if (hayConflictoVacaciones || hayConflictoContrato) {
      Swal.fire('Conflictos encontrados', finalMessage || 'Revise los mensajes de validación para más detalles.', 'error');
      $('#calendar-collapse-body').collapse('hide');
      return;
    }

    // B. Conflicto General: Días Totalmente Bloqueados
    if (hayConflictoGeneral && generalValidationResult.fullyBlockedDaysCount > 0) {
      Swal.fire('Error Bloqueante', 'Se encontraron días completamente bloqueados. Debe desmarcar los días para proceder.', 'error');
      $('#calendar-collapse-body').collapse('hide');
      return;
    }

    // C. Conflicto General: Parcial (Guardado Parcial)
    if (hayConflictoGeneral && generalValidationResult.hasPartialSuggestion) {
      const availableDaysToSave = generalValidationResult.available_days_suggestion || [];
      $('#calendar-collapse-body').collapse('hide');

      Swal.fire({
        title: 'Validación final',
        html: 'No se encontraron conflictos en el personal, en programación se encontraron registros pero <strong> quedan días disponibles</strong> en el rango de fechas para completar.<br><br>¿Desea <strong>completar</strong> la programación para estos días disponibles y guardar de todas formas?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, completar y guardar',
        cancelButtonText: 'No, cancelar',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          // Extraer fechas YYYY-MM-DD
          let fechasDisponibles = availableDaysToSave
            .map(d => d.match(/\((.*?)\)/)?.[1])
            .filter(date => date);
          
          // Eliminar campos 'dias[]' originales
          formData = formData.filter(item => item.name !== 'dias[]');
          
          // Añadir fechas disponibles
          fechasDisponibles.forEach(fecha => {
            formData.push({ name: 'fechas_a_guardar[]', value: fecha });
          });
          
          saveProgramacion(formData, id, url, method);
        } else {
          Swal.fire('Cancelado', 'La programación no fue guardada.', 'info');
        }
      });
      return;
    }

    // D. Sin Conflictos - Guardar Normal
    saveProgramacion(formData, id, url, method);
  });

  // ==========================================
  // BOTÓN VALIDAR DISPONIBILIDAD
  // ==========================================
  $('#btnValidarDisponibilidad').click(async function() {
    const formData = $('#formProgramacion').serializeArray();

    Swal.fire({
      title: 'Validando...',
      text: 'Comprobando la disponibilidad del personal...',
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });
    
    const validationResult = await validarVacaciones(formData);

    if (validationResult.isValid) {
      Swal.fire('Disponibilidad Confirmada', '¡El personal está disponible para el periodo seleccionado!', 'success');
    } else {
      Swal.fire('Error', validationResult.message, 'warning');
    }
  });

  // ==========================================
  // BOTÓN ELIMINAR
  // ==========================================
  $(document).on('click', '.btnEliminar', function() {
    const id = $(this).data('id');
    
    Swal.fire({
    title: '¿Está seguro?',
    text: 'Esta acción eliminará la programación.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
    }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
      url: `/programacion/${id}/destroy`,
      type: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        _method: 'DELETE'
      },
      success: function(res) {
        if (res.success) {
        Swal.fire('Eliminado', res.message, 'success');
        $(`#row_${id}`).fadeOut(400, function() { $(this).remove(); });
        } else {
        Swal.fire('Error', res.message || 'No se pudo eliminar.', 'error');
        }
      },
      error: () => Swal.fire('Error', 'Fallo de conexión al eliminar.', 'error')
      });
    }
    });
  });
  // ==========================================
  // VALIDACIÓN DE PERSONAL ÚNICO
  // ==========================================
  function validarPersonalUnico() {
    const ayudante1Id = parseInt($('#ayudante1_id').val() || 0);
    const ayudante2Id = parseInt($('#ayudante2_id').val() || 0);

    const personalIds = [ayudante1Id, ayudante2Id].filter(id => id > 0);

    if (personalIds.length <= 1) {
      return true;
    }

    const uniqueIds = new Set(personalIds);

    if (uniqueIds.size !== personalIds.length) {
      Swal.fire({
        text: 'Ha seleccionado a la misma persona para ayudante. Por favor, corrija la asignación para continuar.',
        icon: 'warning',
        confirmButtonText: 'Entendido'
      });
      return false;
    }

    return true;
  }

  $('#ayudante1_id, #ayudante2_id').on('change', function() {
    validarPersonalUnico();
  });
  // ==========================================
  // GUARDAR PROGRAMACIÓN
  // ==========================================
  function saveProgramacion(formData, id, url, method) {
    if (method === 'PUT') {
      formData.push({ name: '_method', value: 'PUT' });
    }
    
    Swal.update({
      title: id ? 'Actualizando...' : 'Guardando...',
      text: 'Enviando datos al servidor...',
      showCancelButton: false,
      showConfirmButton: false
    });

    $.ajax({
      url: url,
      type: 'POST',
      data: formData,
      success: function(res) {
        if (res.success) {
          Swal.fire('Éxito', res.message, 'success');
          $('#calendar-collapse-body').collapse('hide');
          $('#modalProgramacion').modal('hide');
          setTimeout(() => location.reload(), 800);
        } else {
          Swal.fire('Error', res.message || 'Error desconocido.', 'error');
        }
      },
      error: function(xhr) {
        let errorMsg = 'Error en el servidor o de conexión.';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          errorMsg = 'Error<br>';
          $.each(xhr.responseJSON.errors, function(key, value) {
            errorMsg += `- ${value[0]}<br>`;
          });
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMsg = xhr.responseJSON.message;
        }
        Swal.fire('Error', errorMsg, 'error');
      }
    });
  }

  }); // FIN DOCUMENT READY

  // ==========================================
  // FUNCIÓN: VER DETALLE (MODAL)
  // ==========================================
  window.verDetalle = function(id) {
    Swal.fire({
      title: 'Cargando...',
      text: 'Obteniendo información de la programación...',
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });

    $.get(`/programacion/${id}/show`, function(res) {
      Swal.close();
      
      if (res.success && res.data) {
        const prog = res.data;
        
        // ✅ FIX: Datos Generales
        $('#detalle_fecha').text(prog.fecha_inicio || 'N/A');
        
        // ✅ FIX: Estado con badge correcto
        const estadoBadge = prog.status_badge || 'badge-secondary';
        const estadoLabel = prog.status_label || 'Desconocido';
        $('#detalle_estado').html(`<span class="badge ${estadoBadge}">${estadoLabel}</span>`);
        
        $('#detalle_zona').text(prog.zona?.nombre || 'N/A');
        $('#detalle_turno').text(prog.turno?.name || 'N/A');
        $('#detalle_vehiculo').text(prog.vehiculo?.codigo || 'N/A');

        // ✅ FIX: Personal Asignado - Carga correcta
        let personalHTML = '';
        if (prog.personal_asignado && prog.personal_asignado.length > 0) {
          // Ordenar: primero conductor, luego ayudantes
          const personal = prog.personal_asignado;
          
          personal.forEach((p, index) => {
            // Determinar rol basado en la función
            let rol = 'Personal';
            if (p.funcion_id) {
              // Aquí puedes ajustar según tus IDs de función
              // Por ejemplo: 1 = Conductor, 2 = Ayudante
              if (p.funcion?.nombre === 'Conductor') {
                rol = 'Conductor';
              } else if (p.funcion?.nombre === 'Ayudante') {
                rol = `Ayudante ${index === 0 ? 1 : index}`;
              } else {
                rol = p.funcion?.nombre || `Personal ${index + 1}`;
              }
            } else {
              // Fallback: asumir primer registro es conductor
              rol = index === 0 ? 'Conductor' : `Ayudante ${index}`;
            }
            
            personalHTML += `
              <tr>
                <td><strong>${rol}</strong></td>
                <td>${p.nombre_completo || ((p.nombres || '') + ' ' + (p.apellidos || '')) || 'N/A'}</td>
              </tr>
            `;
          });
        } else {
          personalHTML = '<tr><td colspan="2" class="text-center text-muted">No hay personal asignado</td></tr>';
        }
        $('#detallePersonalBody').html(personalHTML);

        // ✅ Historial de Cambios (sin cambios)
        let historialHTML = '';
        if (prog.cambios && prog.cambios.length > 0) {
          prog.cambios.forEach(c => {
            const fechaCambio = c.created_at ? new Date(c.created_at).toLocaleString('es-PE') : 'N/A';
            historialHTML += `
              <tr>
                <td>${fechaCambio}</td>
                <td>${c.valor_anterior_nombre || 'N/A'}</td>
                <td>${c.valor_nuevo_nombre || 'N/A'}</td>
                <td>${c.motivo?.nombre || 'Sin motivo'}</td>
              </tr>
            `;
          });
        } else {
          historialHTML = '<tr><td colspan="4" class="text-center text-muted">No hay cambios registrados</td></tr>';
        }
        $('#detalleHistorialBody').html(historialHTML);

        $('#modalVerDetalle').modal('show');
      } else {
        Swal.fire('Error', 'No se pudo cargar la información.', 'error');
      }
    }).fail(() => {
      Swal.close();
      Swal.fire('Error', 'Fallo de conexión.', 'error');
    });
  };

  // ==========================================
  // FIX: FUNCIÓN EDITAR PROGRAMACIÓN (Reemplazar en tu index)
  // ==========================================
  let cambiosTemp = [];

  window.editarProgramacion = function(id) {
    cambiosTemp = [];
    
    Swal.fire({
      title: 'Cargando...',
      text: 'Obteniendo información...',
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });

    $.get(`/programacion/${id}/show`, function(res) {
      Swal.close();
      
      if (res.success && res.data) {
        const prog = res.data;
        
        $('#edit_programacion_id').val(prog.id);
        $('#edit_turno_actual').val(prog.turno?.name || 'N/A').data('id', prog.turno_id);
        $('#edit_vehiculo_actual').val(prog.vehiculo?.codigo || 'N/A').data('id', prog.vehiculo_id);

        // ✅ FIX: Cargar personal actual correctamente
        let personalHTML = '<option value="">Seleccione personal a cambiar</option>';
        
        if (prog.personal_asignado && prog.personal_asignado.length > 0) {
          prog.personal_asignado.forEach((p, index) => {
            // Determinar rol
            let rol = 'Personal';
            if (p.funcion?.nombre === 'Conductor') {
              rol = 'Conductor';
            } else if (p.funcion?.nombre === 'Ayudante') {
              rol = `Ayudante ${index === 0 ? 1 : index}`;
            } else {
              rol = index === 0 ? 'Conductor' : `Ayudante ${index}`;
            }
            
            const nombreCompleto = p.nombre_completo || ((p.nombres || '') + ' ' + (p.apellidos || '')) || 'Sin nombre';
            personalHTML += `<option value="${p.id}" data-nombre="${nombreCompleto}">${rol}: ${nombreCompleto}</option>`;
          });
        }
        
        $('#edit_personal_actual').html(personalHTML);

        // ✅ Cargar opciones de nuevo personal
        let nuevoPersonalHTML = '<option value="">Seleccione nuevo personal</option>';
        
        // Aquí debes poner tu lista de conductores y ayudantes
        // Ejemplo (ajusta según tus datos):
        @foreach ($conductores ?? [] as $c)
          nuevoPersonalHTML += `<option value="{{ $c->id }}" data-nombre="{{ $c->nombre_completo }}">{{ $c->nombre_completo }} (Conductor)</option>`;
        @endforeach
        @foreach ($ayudantes ?? [] as $a)
          nuevoPersonalHTML += `<option value="{{ $a->id }}" data-nombre="{{ $a->nombre_completo }}">{{ $a->nombre_completo }} (Ayudante)</option>`;
        @endforeach
        
        $('#edit_nuevo_personal').html(nuevoPersonalHTML);

        // Limpiar tabla de cambios
        $('#tablaCambiosRegistrados').html('<tr id="noCambiosRow"><td colspan="6" class="text-center text-muted">No hay cambios registrados</td></tr>');

        $('#modalEditarProgramacion').modal('show');
      } else {
        Swal.fire('Error', 'No se pudo cargar la información.', 'error');
      }
    }).fail(() => {
      Swal.close();
      Swal.fire('Error', 'Fallo de conexión.', 'error');
    });
  };
  // ==========================================
  // AGREGAR CAMBIO: TURNO
  // ==========================================
  window.agregarCambioTurno = function() {
    const nuevoTurnoId = $('#edit_nuevo_turno').val();
    
    if (!nuevoTurnoId) {
      Swal.fire('Atención', 'Debe seleccionar un nuevo turno.', 'warning');
      return;
    }

    const turnoActualId = $('#edit_turno_actual').data('id');
    const turnoActualNombre = $('#edit_turno_actual').val();
    const nuevoTurnoNombre = $('#edit_nuevo_turno option:selected').data('nombre');

    const cambio = {
      tipo_cambio: 'turno',
      valor_anterior: turnoActualId,
      valor_anterior_nombre: turnoActualNombre,
      valor_nuevo: nuevoTurnoId,
      valor_nuevo_nombre: nuevoTurnoNombre,
      motivo_id: null,
      notas: ''
    };

    cambiosTemp.push(cambio);
    actualizarTablaCambios();
    
    $('#edit_nuevo_turno').val('');
    Swal.fire('Agregado', 'Cambio de turno agregado. No olvide seleccionar un motivo.', 'success');
  };

  // ==========================================
  // AGREGAR CAMBIO: VEHÍCULO
  // ==========================================
  window.agregarCambioVehiculo = function() {
    const nuevoVehiculoId = $('#edit_nuevo_vehiculo').val();
    
    if (!nuevoVehiculoId) {
      Swal.fire('Atención', 'Debe seleccionar un nuevo vehículo.', 'warning');
      return;
    }

    const vehiculoActualId = $('#edit_vehiculo_actual').data('id');
    const vehiculoActualNombre = $('#edit_vehiculo_actual').val();
    const nuevoVehiculoNombre = $('#edit_nuevo_vehiculo option:selected').data('nombre');

    const cambio = {
      tipo_cambio: 'vehiculo',
      valor_anterior: vehiculoActualId,
      valor_anterior_nombre: vehiculoActualNombre,
      valor_nuevo: nuevoVehiculoId,
      valor_nuevo_nombre: nuevoVehiculoNombre,
      motivo_id: null,
      notas: ''
    };

    cambiosTemp.push(cambio);
    actualizarTablaCambios();
    
    $('#edit_nuevo_vehiculo').val('');
    Swal.fire('Agregado', 'Cambio de vehículo agregado. No olvide seleccionar un motivo.', 'success');
  };

  // ==========================================
  // AGREGAR CAMBIO: PERSONAL
  // ==========================================
  window.agregarCambioPersonal = function() {
    const personalActualId = $('#edit_personal_actual').val();
    const nuevoPersonalId = $('#edit_nuevo_personal').val();
    
    if (!personalActualId || !nuevoPersonalId) {
      Swal.fire('Atención', 'Debe seleccionar el personal actual y el nuevo personal.', 'warning');
      return;
    }

    const personalActualNombre = $('#edit_personal_actual option:selected').data('nombre');
    const nuevoPersonalNombre = $('#edit_nuevo_personal option:selected').data('nombre');

    const cambio = {
      tipo_cambio: 'personal',
      valor_anterior: personalActualId,
      valor_anterior_nombre: personalActualNombre,
      valor_nuevo: nuevoPersonalId,
      valor_nuevo_nombre: nuevoPersonalNombre,
      motivo_id: null,
      notas: ''
    };

    cambiosTemp.push(cambio);
    actualizarTablaCambios();
    
    $('#edit_personal_actual, #edit_nuevo_personal').val('');
    Swal.fire('Agregado', 'Cambio de personal agregado. No olvide seleccionar un motivo.', 'success');
  };

  // ==========================================
  // ACTUALIZAR TABLA DE CAMBIOS
  // ==========================================
  function actualizarTablaCambios() {
    if (cambiosTemp.length === 0) {
      $('#tablaCambiosRegistrados').html('<tr id="noCambiosRow"><td colspan="6" class="text-center text-muted">No hay cambios registrados</td></tr>');
      return;
    }

    $('#noCambiosRow').remove();

    let html = '';
    cambiosTemp.forEach((c, index) => {
      const tipoFormateado = c.tipo_cambio === 'turno' ? 'Turno' : c.tipo_cambio === 'vehiculo' ? 'Vehículo' : 'Personal';
      
      html += `
        <tr>
          <td>${tipoFormateado}</td>
          <td>${c.valor_anterior_nombre}</td>
          <td>${c.valor_nuevo_nombre}</td>
          <td>
            <select class="form-control form-control-sm cambio-motivo" data-index="${index}" required>
              <option value="">Seleccione motivo</option>
              @foreach ($motivos as $m)
                <option value="{{ $m->id }}">{{ $m->nombre }}</option>
              @endforeach
            </select>
          </td>
          <td>
            <textarea class="form-control form-control-sm cambio-notas" data-index="${index}" rows="2" placeholder="Notas adicionales"></textarea>
          </td>
          <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarCambio(${index})">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
      `;
    });

    $('#tablaCambiosRegistrados').html(html);
  }

  // ==========================================
  // ELIMINAR CAMBIO
  // ==========================================
  window.eliminarCambio = function(index) {
    cambiosTemp.splice(index, 1);
    actualizarTablaCambios();
    Swal.fire('Eliminado', 'Cambio eliminado de la lista.', 'info');
  };

  // ==========================================
  // GUARDAR CAMBIOS (ENVIAR AL SERVIDOR)
  // ==========================================
  window.guardarCambios = function() {
    if (cambiosTemp.length === 0) {
      Swal.fire('Atención', 'No hay cambios para guardar.', 'warning');
      return;
    }

    // Validar que todos los cambios tengan motivo
    let motivosFaltantes = false;
    $('.cambio-motivo').each(function() {
      const index = $(this).data('index');
      const motivoId = $(this).val();
      const notas = $(`.cambio-notas[data-index="${index}"]`).val();

      if (!motivoId) {
        motivosFaltantes = true;
        return false;
      }

      cambiosTemp[index].motivo_id = motivoId;
      cambiosTemp[index].notas = notas;
    });

    if (motivosFaltantes) {
      Swal.fire('Error', 'Debe seleccionar un motivo para cada cambio.', 'error');
      return;
    }

    const programacionId = $('#edit_programacion_id').val();
    
    Swal.fire({
      title: 'Guardando cambios...',
      text: 'Procesando la información...',
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });

    $.ajax({
      url: `/programacion/${programacionId}/update-con-cambios`,
      type: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        _method: 'PUT',
        cambios: cambiosTemp,
        turno_id: cambiosTemp.find(c => c.tipo_cambio === 'turno')?.valor_nuevo,
        vehiculo_id: cambiosTemp.find(c => c.tipo_cambio === 'vehiculo')?.valor_nuevo,
        personal_changes: cambiosTemp
          .filter(c => c.tipo_cambio === 'personal')
          .map(c => ({ anterior_id: c.valor_anterior, nuevo_id: c.valor_nuevo })),
      },
      success: function(res) {
        if (res.success) {
          Swal.fire('Éxito', res.message, 'success');
          $('#modalEditarProgramacion').modal('hide');
          setTimeout(() => location.reload(), 800);
        } else {
          Swal.fire('Error', res.message || 'Error al guardar cambios.', 'error');
        }
      },
      error: function(xhr) {
        let errorMsg = 'Error al guardar los cambios.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMsg = xhr.responseJSON.message;
        }
        Swal.fire('Error', errorMsg, 'error');
      }
    });
  };

  // ==========================================
  // MODIFICACIÓN MASIVA
  // ==========================================

  // Abrir modal de modificación masiva
  $('#btnModificacionMasiva').click(function() {
    $('#formModificacionMasiva')[0].reset();
    $('#masiva-validation-messages').empty();
    $('#modalModificacionMasiva').modal('show');
  });

  // Función de debug para verificar qué programaciones se encontrarían
  window.debugBusquedaMasiva = async function() {
    const fechaInicio = $('#masiva_fecha_inicio').val();
    const fechaFin = $('#masiva_fecha_fin').val();
    const zonaId = $('#masiva_zona_id').val() || '';

    if (!fechaInicio || !fechaFin) {
      alert('Por favor ingrese ambas fechas');
      return;
    }

    try {
      const response = await $.ajax({
        url: '/programacion/debug-busqueda',
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}',
          fecha_inicio: fechaInicio,
          fecha_fin: fechaFin,
          zona_id: zonaId || null
        }
      });

      console.log('Debug results:', response);
      alert(`
  DEBUG BÚSQUEDA:
  - Fecha inicio: ${response.debug.fecha_inicio}
  - Fecha fin: ${response.debug.fecha_fin}
  - Zona ID: ${response.debug.zona_id}

  RESULTADOS:
  - Total programaciones (todas): ${response.total_todas}
  - Total programaciones (activas, status=1): ${response.total_activas}
  ${response.total_con_zona !== null ? `- Total programaciones (con zona filtro): ${response.total_con_zona}` : ''}

  Ver en consola del navegador (F12) para detalles completos.
      `.trim());
    } catch(err) {
      console.error('Error en debug:', err);
      alert('Error en debug: ' + (err.responseJSON?.message || err.statusText));
    }
  };

  // Mostrar/Ocultar campos según tipo de cambio seleccionado
  $('#masiva_tipo_cambio').on('change', function() {
    const tipoCambio = $(this).val();
    
    // Ocultar todos los campos dinámicos
    $('#cambio_conductor_fields, #cambio_ocupante_fields, #cambio_turno_fields, #cambio_vehiculo_fields').hide();
    
    // Limpiar validaciones
    $('#masiva_conductor_nuevo, #masiva_ocupante_nuevo, #masiva_turno_nuevo, #masiva_vehiculo_nuevo').removeAttr('required').val('');
    
    // Mostrar campos según tipo de cambio
    if (tipoCambio === 'conductor') {
      $('#cambio_conductor_fields').show();
      $('#masiva_conductor_nuevo').attr('required', 'required');
    } else if (tipoCambio === 'ocupante') {
      $('#cambio_ocupante_fields').show();
      $('#masiva_ocupante_nuevo').attr('required', 'required');
    } else if (tipoCambio === 'turno') {
      $('#cambio_turno_fields').show();
      $('#masiva_turno_nuevo').attr('required', 'required');
    } else if (tipoCambio === 'vehiculo') {
      $('#cambio_vehiculo_fields').show();
      $('#masiva_vehiculo_nuevo').attr('required', 'required');
    }
  });

  // Submit del formulario de modificación masiva
  $('#formModificacionMasiva').submit(async function(e) {
    e.preventDefault();
    
    const $form = $(this);
    const formData = {
      _token: '{{ csrf_token() }}',
      fecha_inicio: $('#masiva_fecha_inicio').val(),
      fecha_fin: $('#masiva_fecha_fin').val(),
      zona_id: $('#masiva_zona_id').val() || '', // Enviar cadena vacía si no hay valor
      tipo_cambio: $('#masiva_tipo_cambio').val(),
      motivo_id: $('#masiva_motivo_id').val(),
      notas: $('#masiva_notas').val() || '',
    };

    // Agregar campos específicos según tipo de cambio
    const tipoCambio = formData.tipo_cambio;
    if (tipoCambio === 'conductor') {
      formData.conductor_nuevo = $('#masiva_conductor_nuevo').val();
    } else if (tipoCambio === 'ocupante') {
      formData.ocupante_nuevo = $('#masiva_ocupante_nuevo').val();
    } else if (tipoCambio === 'turno') {
      formData.turno_nuevo = $('#masiva_turno_nuevo').val();
    } else if (tipoCambio === 'vehiculo') {
      formData.vehiculo_nuevo = $('#masiva_vehiculo_nuevo').val();
    }

    // Validación básica
    if (!formData.fecha_inicio || !formData.fecha_fin || !formData.tipo_cambio || !formData.motivo_id) {
      Swal.fire('Validación', 'Complete todos los campos requeridos.', 'warning');
      return;
    }

    if (new Date(formData.fecha_fin) < new Date(formData.fecha_inicio)) {
      Swal.fire('Validación', 'La fecha de fin debe ser mayor o igual a la fecha de inicio.', 'warning');
      return;
    }

    // Validar que se haya seleccionado un nuevo valor
    let nuevoValor = null;
    if (tipoCambio === 'conductor') {
      nuevoValor = formData.conductor_nuevo;
    } else if (tipoCambio === 'ocupante') {
      nuevoValor = formData.ocupante_nuevo;
    } else if (tipoCambio === 'turno') {
      nuevoValor = formData.turno_nuevo;
    } else if (tipoCambio === 'vehiculo') {
      nuevoValor = formData.vehiculo_nuevo;
    }

    if (!nuevoValor) {
      Swal.fire('Validación', 'Debe seleccionar el nuevo valor para el tipo de cambio.', 'warning');
      return;
    }

    // Mostrar loading
    Swal.fire({
      title: 'Procesando...',
      icon: 'info',
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });

    try {
      // Realizar solicitud AJAX
      const response = await $.ajax({
        url: '/programacion/modificacion-masiva',
        type: 'POST',
        data: formData,
      });

      console.log('Respuesta del servidor:', response);
      if (response.success) {
        let htmlContent = `<div class="alert alert-success">${response.message}</div>`;
        
        // Mostrar estadísticas si están disponibles
        if (response.datos) {
          htmlContent += '<div class="alert alert-info mt-3">';
          htmlContent += '<strong>📊 Resumen de operación:</strong>';
          htmlContent += '<ul class="mt-2" style="padding-left: 20px;">';
          htmlContent += `<li>Programaciones encontradas: <strong>${response.datos.programaciones_encontradas}</strong></li>`;
          htmlContent += `<li>Programaciones actualizadas: <strong>${response.datos.programaciones_actualizadas}</strong></li>`;
          htmlContent += `<li>Cambios registrados: <strong>${response.datos.cambios_registrados}</strong></li>`;
          if (response.datos.programaciones_saltadas > 0) {
            htmlContent += `<li>Programaciones sin cambios: <strong>${response.datos.programaciones_saltadas}</strong> (ya tenían asignado el valor)</li>`;
          }
          htmlContent += '</ul>';
          htmlContent += '</div>';
        }
        
        Swal.fire({
          title: '✅ Éxito',
          html: htmlContent,
          icon: 'success',
          allowOutsideClick: true
        }).then(() => {
          $('#formModificacionMasiva')[0].reset();
          $('#modalModificacionMasiva').modal('hide');
          setTimeout(() => location.reload(), 800);
        });
      } else {
        // Mostrar errores de forma clara
        let htmlContent = `<div class="alert alert-danger" style="margin-bottom: 15px;"><strong>${response.message}</strong></div>`;
        
        // Si hay conflictos personales, listarlos con más detalle
        if (response.tipo === 'conflictos_personal' && response.conflictos && response.conflictos.length > 0) {
          htmlContent += '<div class="alert alert-warning" style="margin-bottom: 15px;">';
          htmlContent += '<h6 style="margin-top: 0; margin-bottom: 10px;"><i class="fas fa-exclamation-triangle"></i> Conflictos detectados:</h6>';
          htmlContent += '<div style="background-color: #fff; border: 1px solid #ffc107; border-radius: 4px; padding: 10px; max-height: 300px; overflow-y: auto;">';
          htmlContent += '<ul style="margin: 0; padding-left: 20px;">';
          
          response.conflictos.forEach((conflicto, index) => {
            htmlContent += `<li style="margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid #f0f0f0;">`;
            htmlContent += `<strong>📌 Programación #${conflicto.programacion_id}</strong><br>`;
            htmlContent += `<span style="font-size: 0.9em; color: #666;">📅 Fecha: ${conflicto.fecha}</span><br>`;
            htmlContent += `<span style="font-size: 0.9em; color: #d32f2f;"><strong>❌ Conflicto:</strong> ${conflicto.motivo}</span>`;
            htmlContent += `</li>`;
          });
          
          htmlContent += '</ul>';
          htmlContent += '</div>';
          htmlContent += `<p style="margin-top: 10px; margin-bottom: 0; font-size: 0.9em; color: #666;"><strong>Total:</strong> ${response.conflictos.length} programación(es) con conflictos</p>`;
          htmlContent += '</div>';
        }
        
        Swal.fire({
          title: '❌ No se pudo completar la operación',
          html: htmlContent,
          icon: 'error',
          allowOutsideClick: true,
          customClass: {
            popup: 'swal2-wide'
          }
        });
      }
    } catch (xhr) {
      console.log('Error en AJAX:', xhr);
      console.log('Status:', xhr.status);
      console.log('Response JSON:', xhr.responseJSON);
      
      let errorMsg = 'Error al procesar la modificación masiva.';
      let htmlContent = '';
      
      // Si es un error 422 (validación), obtener la respuesta JSON
      if (xhr.status === 422 && xhr.responseJSON) {
        const responseData = xhr.responseJSON;
        errorMsg = responseData.message || 'Se encontraron errores de validación.';
        
        // Si hay conflictos personales, listarlos
        if (responseData.tipo === 'conflictos_personal' && responseData.conflictos && responseData.conflictos.length > 0) {
          htmlContent = `<div class="alert alert-danger" style="margin-bottom: 15px;"><strong>${errorMsg}</strong></div>`;
          htmlContent += '<div class="alert alert-warning" style="margin-bottom: 15px;">';
          htmlContent += '<h6 style="margin-top: 0; margin-bottom: 10px;"><i class="fas fa-exclamation-triangle"></i> Conflictos detectados:</h6>';
          htmlContent += '<div style="background-color: #fff; border: 1px solid #ffc107; border-radius: 4px; padding: 10px; max-height: 400px; overflow-y: auto;">';
          htmlContent += '<ul style="margin: 0; padding-left: 20px;">';
          
          responseData.conflictos.forEach((conflicto, index) => {
            htmlContent += `<li style="margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid #f0f0f0;">`;
            htmlContent += `<strong>📌 Programación #${conflicto.programacion_id}</strong><br>`;
            htmlContent += `<span style="font-size: 0.9em; color: #666;">📅 Fecha: ${conflicto.fecha}</span><br>`;
            htmlContent += `<span style="font-size: 0.9em; color: #d32f2f;"><strong>❌ Conflicto:</strong> ${conflicto.motivo}</span>`;
            htmlContent += `</li>`;
          });
          
          htmlContent += '</ul>';
          htmlContent += '</div>';
          htmlContent += `<p style="margin-top: 10px; margin-bottom: 0; font-size: 0.9em; color: #666;"><strong>Total:</strong> ${responseData.conflictos.length} programación(es) con conflictos</p>`;
          htmlContent += '</div>';
        } else {
          htmlContent = `<div class="alert alert-danger">${errorMsg}</div>`;
        }
      } else if (xhr.status === 404 && xhr.responseJSON) {
        errorMsg = xhr.responseJSON.message || 'No se encontraron programaciones.';
        htmlContent = `<div class="alert alert-danger">${errorMsg}</div>`;
        
        // Si hay información de debug, mostrarla
        if (xhr.responseJSON.debug_info) {
          htmlContent += '<div class="alert alert-info mt-3">';
          htmlContent += '<strong>ℹ️ Información de debug:</strong>';
          htmlContent += '<ul style="margin: 0; margin-top: 10px; padding-left: 20px;">';
          htmlContent += `<li>Total en rango de fechas: ${xhr.responseJSON.debug_info.total_en_rango}</li>`;
          htmlContent += `<li>Canceladas: ${xhr.responseJSON.debug_info.canceladas}</li>`;
          htmlContent += '</ul>';
          htmlContent += '</div>';
        }
      } else if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMsg = xhr.responseJSON.message;
        htmlContent = `<div class="alert alert-danger">${errorMsg}</div>`;
        
        if (xhr.responseJSON.details) {
          htmlContent += `<div class="alert alert-warning mt-3"><strong>Detalles:</strong> ${xhr.responseJSON.details}</div>`;
        }
      } else {
        htmlContent = `<div class="alert alert-danger">${errorMsg}</div>`;
      }

      Swal.fire({
        title: '❌ Error',
        html: htmlContent,
        icon: 'error',
        allowOutsideClick: true,
        customClass: {
          popup: 'swal2-wide'
        }
      });
    }
  });

</script>

@endpush





