@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">

<style>
.modal-header {
 background-color: #3f6791;
 color: #fff;
 border-top-left-radius: .3rem;
 border-top-right-radius: .3rem;
}
.modal-header .close span {
 color: #fff !important;
}

/* Transición para la rotación del icono */
.toggle-icon {
    transition: transform 0.3s ease;
}

/* Rota el icono cuando el enlace NO tiene la clase 'collapsed' (es decir, cuando está expandido) */
/* El ID del header es genérico, por lo que usaremos el selector de clase en el enlace (a) */
.grupo-card a:not(.collapsed) .toggle-icon {
    transform: rotate(180deg);
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
    color: #000000 !important; /*  Color Negro  */
    font-size: 16px !important; /*  Tamaño 18px  */
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
.fc-daygrid-day.fc-day-other {
    /*  Color de fondo gris muy claro */
    background-color: #f8f8f8 !important; 
    /* Eliminar cualquier opacidad que pueda oscurecer el fondo */
    opacity: 1 !important; 
}

/* Estilo para los números de día y el texto */
/* Esto asegura que el número del día sea visible, pero atenuado (gris más oscuro) */
.fc-daygrid-day.fc-day-other .fc-daygrid-day-top a,
.fc-daygrid-day.fc-day-other .fc-daygrid-day-number {
    /*  Color de texto gris atenuado */
    color: #f8f8f80e !important; 
    /* Asegurar que no se aplique opacidad al texto */
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
    font-weight: bold !important; /*  ESTO PONE EL TEXTO EN NEGRITA  */
}

.fc-event-title-container {
    /*  Establece el tamaño de fuente a 16px */
    font-size: 16px !important; 
    /* Asegura que el texto se centre si hay espacio */
    text-align: center;
}

/*FIN CALENDARIO */

.turno-selector {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #dee2e6;
}

.btn-turno {
    min-width: 150px;
    padding: 12px 24px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-turno:not(.active) {
    background-color: white;
    color: #495057;
    border: 2px solid #dee2e6;
}

.btn-turno.active {
    transform: scale(1.05);
}

.grupo-card {
    border: 2px solid #3f6791;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    background-color: #ffffff;
    transition: all 0.3s ease;
}

.grupo-card:hover {
    transform: translateY(-3px);
}

.grupo-card-header {
    background: linear-gradient(135deg, #3f6791 0%, #0056b3 100%);
    color: white;
    padding: 15px;
    margin: -20px -20px 20px -20px;
    border-radius: 8px 8px 0 0;
    font-weight: bold;
    font-size: 16px;
    display: flex; 
    justify-content: space-between; /* Envía el primer elemento a la izquierda y el último a la derecha */
    align-items: center;
}

.info-badge {
    background-color: #e9ecef;
    padding: 8px 15px;
    border-radius: 5px;
    margin-bottom: 10px;
    border-left: 4px solid #3f6791;
}

.info-badge strong {
    color: #3f6791;
}

.select2-container {
    width: 100% !important;
}

.select2-container--bootstrap4 .select2-selection {
    min-height: 38px !important;
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
}

.select2-container--bootstrap4 .select2-selection--single {
    height: 38px !important;
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    line-height: 36px !important;
    padding-left: 12px !important;
}

/* ... (tus estilos select2 existentes) ... */

.select2-selection__arrow {
    margin-top: -5px;
}

.select2-container--bootstrap4.select2-container--focus .select2-selection {
    border-color: #80bdff !important;
}

.select2-dropdown {
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
}

.select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
    background-color: #3f6791 !important;
    color: white !important;
}

.btn-registrar {
    background-color: #28a745;
    color: white;
    padding: 12px 24px;
    font-size: 1rem;
    border: none;
    transition: all 0.3s ease;
}

.btn-registrar:hover:not(:disabled) {
    background-color: #218838;
    color: white;
}

.btn-registrar:disabled {
    background-color: #6c757d;
    color: white;
    cursor: not-allowed;
    opacity: 0.65;
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
}

.validacion-container {
    margin-top: 15px;
    padding: 0;
}

.validacion-container .alert {
    margin-bottom: 10px;
    padding: 12px 15px;
    border-left: 4px solid;
    font-size: 0.95rem;
}

.validacion-container .alert-success {
    background-color: #d4edda;
    border-left-color: #28a745;
    color: #155724;
}

.validacion-container .alert-danger {
    background-color: #f8d7da;
    border-left-color: #dc3545;
    color: #721c24;
}

.validacion-container .alert-warning {
    background-color: #fff3cd;
    border-left-color: #ffc107;
    color: #856404;
}

.validacion-container .alert strong {
    font-weight: 600;
}

#grupos-container:empty::before {
    content: "Seleccione un turno (MAÑANA o TARDE) para ver los grupos de personal disponibles.";
    display: block;
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    font-style: italic;
    font-size: 1.1rem;
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    margin: 20px 0;
}

</style>
@endpush

@section('content')
<div class="content-header">
 <div class="container-fluid">
  <div class="row mb-2">
   <div class="col-sm-6">
    {{-- <h1 class="m-0">Programación Masiva</h1> --}}
   </div>
   <div class="col-sm-6">
    <ol class="breadcrumb float-sm-right">
     <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
     <li class="breadcrumb-item"><a href="{{ route('programaciones.index') }}">Programaciones</a></li>
     <li class="breadcrumb-item active">Programación Masiva</li>
    </ol>
   </div>
  </div>
  <div class="row">
    
  </div>
 </div>
</div>

<div class="content">
 <div class="container-fluid">
  <form id="formProgramacionMasiva">
    @csrf
    
    <!-- Sección de Turno y Fechas -->
    <div class="card">
      <div class="card-body turno-selector">
        <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0" style="font-size: 17px;"><strong> Nueva programación masiva </strong></h5>

   <a href="{{ route('programaciones.index') }}" class="btn btn-secondary" style="font-size: 15px;">
    <i class="fas fa-arrow-left"></i> &nbsp;
    <span> Volver </span>
</a>
</div>
        <hr class="my-4">

        
        <!-- Selección de Turno -->
        <div class="row mb-3">
    <div class="col-md-12">
        <label class="font-weight-bold mb-3">Seleccionar Turno: <span class="text-danger">*</span></label>
        
        <div class="d-flex justify-content-center gap-3">
            
            {{-- Iterar sobre la colección de turnos --}}
            @foreach ($turnos as $turno)
                <button 
                    type="button" 
                    class="btn btn-turno btn-outline-secondary {{ $loop->iteration > 1 ? 'ml-3' : '' }}" 
                    data-turno="{{ strtolower($turno->name) }}"
                    data-id="{{ $turno->id }}" style="font-size: 16px;">
                    
                    {{ mb_strtoupper($turno->name) }}
                </button>
            @endforeach
            
        </div>
        
        <input type="hidden" name="turno_id" id="turno_id" required>
    </div>
</div>

        <hr class="my-4">

        <!-- Fechas -->
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="fecha_inicio"><i class="fas fa-calendar-day text-success mr-1"></i> Fecha de Inicio: <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="fecha_fin"><i class="fas fa-calendar-day text-danger mr-1"></i> Fecha de Fin: <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
            </div>
          </div>
          <div class="col-md-4" style="padding-top: 25px;">
            <div class="btn-group btn-block" role="group">
              <button type="button" class="btn" id="btnValidarDisponibilidad" style="font-size: 15px; background-color: #3f6791; color: white">
                <i class="fas fa-check-circle"></i>&nbsp; Validar
              </button>
              <button type="submit" class="btn btn-registrar" id="btnRegistrarProgramacion" style="font-size: 15px;" disabled>
                <i class="fas fa-save"></i> &nbsp; Registrar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>




    <!-- Contenedor de Grupos de Personal -->
    <div id="grupos-container">
      <!-- Las tarjetas de grupos se agregarán aquí dinámicamente -->
    </div>


  </form>
 </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
const grupos = @json($grupos);
const zonas = @json($zonas);
const turnos = @json($turnos);
const vehiculos = @json($vehiculos);
const conductores = @json($conductores);
const ayudantes = @json($ayudantes);

let turnoSeleccionado = null;
let validacionCompletada = false;

$(document).ready(function() {
    // Manejo de botones de turno
    $('.btn-turno').click(function() {
        $('.btn-turno').removeClass('active');
        $(this).addClass('active');
        
        const turnoNombre = $(this).data('turno');
        const turno = turnos.find(t => t.name.toLowerCase() === turnoNombre);
        
        if (turno) {
            turnoSeleccionado = turno;
            $('#turno_id').val(turno.id);
            
            // Cargar grupos automáticamente al seleccionar el turno
            cargarGruposPersonal();
        }
    });

    // Validar disponibilidad
    $('#btnValidarDisponibilidad').click(function() {
        validarYMostrarGrupos();
    });

    $(document).on('click', '.btn-eliminar-grupo', function() {
        // Obtenemos la tarjeta del grupo
        const $card = $(this).closest('.grupo-card');
        
        // Usamos SweetAlert para confirmar la acción
        Swal.fire({
            title: '¿Está seguro?',
            text: "Va a eliminar este grupo de la programación masiva.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545', 
            cancelButtonColor: '#6c757d', 
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // *** ACCIÓN CLAVE: Elimina el contenedor de columna completo (.col-md-4) ***
                // Esto mantiene la estructura de 3 columnas por fila
                $card.closest('.col-md-4').remove();
                
                // Reseteamos la validación para forzar una nueva verificación antes de guardar
                if (validacionCompletada) {
                    validacionCompletada = false;
                    $('#btnRegistrarProgramacion').prop('disabled', true);
                }
                
                // Mostrar alerta de éxito
                Swal.fire(
                    'Eliminado!',
                    'El grupo ha sido eliminado de la lista de programación.',
                    'success'
                );
            }
        });
    });

    // Resetear validación cuando cambien los selects de personal o fechas
    // masiva.blade.php (Alrededor de la línea 330)

// Resetear validación cuando cambien los selects de personal o fechas
        $(document).on('change', '.select-conductor, .select-ayudante1, .select-ayudante2, .select-vehiculo, #fecha_inicio, #fecha_fin', function() {
            
            // --- NUEVO CONSOLE.LOG AQUÍ ---
            const element = $(this);
            const elementName = element.attr('name') || element.attr('id');
            const elementValue = element.val();
            
            console.log(`[CAMBIO DETECTADO] Elemento: ${elementName}, Nuevo Valor (ID): ${elementValue}`);
            // --------------------------------
            
            if (validacionCompletada) {
                validacionCompletada = false;
                $('#btnRegistrarProgramacion').prop('disabled', true);
                // Limpiar validaciones mostradas en los grupos
                $('.validacion-container').remove();
                // Ocultar los calendarios de conflictos (si están visibles)
                $('[id^="calendar-conflict-group-"]').slideUp(400); 
            }
});

    // Submit del formulario
    $('#formProgramacionMasiva').submit(function(e) {
        e.preventDefault();
        guardarProgramacionMasiva();
    });
});


// ----------------------------------------------------
//  FUNCIÓN PARA INICIALIZAR EL MINI CALENDARIO POR GRUPO 
// -

// Asegúrate de que esta función exista FUERA de inicializarMiniCalendario
const cleanDateString = (dateStr) => {
    if (typeof dateStr === 'string') {
        return dateStr.split('T')[0];
    }
    return null;
};

// Mapeo de días en español
const diasMap = {
    0: 'Domingo',
    1: 'Lunes',
    2: 'Martes',
    3: 'Miércoles',
    4: 'Jueves',
    5: 'Viernes',
    6: 'Sábado'
};

/**
 * Inicializa el mini calendario de conflictos para un grupo específico.
 * @param {string} grupoId - ID del grupo.
 * @param {string} fechaInicio - Fecha de inicio del rango.
 * @param {string} fechaFin - Fecha de fin del rango.
 * @param {Array} eventosConflictos - Lista de eventos que causan conflicto (rojo).
 */
function inicializarMiniCalendario(grupoId, fechaInicio, fechaFin, eventosConflictos) {
    const calendarContainerId = `calendar-conflict-group-${grupoId}`;
    const miniCalendarId = `mini-calendar-${grupoId}`;
    const collapseId = `collapse-calendar-group-${grupoId}`; 

    const miniCalendarEl = document.getElementById(miniCalendarId);
    const collapseEl = document.getElementById(collapseId);

    if (!miniCalendarEl || !collapseEl) return;

    miniCalendarEl.innerHTML = ''; 
    
    // Limpiamos las fechas de inicio y fin del rango de entrada
    const cleanInicio = cleanDateString(fechaInicio);
    const cleanFin = cleanDateString(fechaFin);

    // 1. PREPARACIÓN: Identificar fechas con conflictos (lógica de eventos se mantiene)
    const conflictDates = new Set(eventosConflictos.map(e => cleanDateString(e.start)).filter(s => s)); 
    
    // ... (lógica para eventosAjustados se mantiene igual)
    const eventosAjustados = eventosConflictos.map(e => {
        const cleanStart = cleanDateString(e.start);
        if (!cleanStart) {
            return null;
        }
        
        const startDate = new Date(cleanStart + 'T00:00:00'); 

        if (isNaN(startDate.getTime())) {
             console.error('Fecha de inicio de conflicto inválida (Invalid Date):', e.start);
             return null; 
        }
        
        const endDate = new Date(startDate);
        endDate.setDate(endDate.getDate() + 1); 
        
        return {
            ...e,
            start: cleanStart, 
            end: endDate.toISOString().split('T')[0],
            eventType: 'programado' 
        };
    }).filter(e => e); 
    
    const fechaInicioObj = new Date(cleanInicio + 'T00:00:00');
    const fechaFinObj = new Date(cleanFin + 'T00:00:00');

    // 2. CÁLCULO: Determinar días a marcar como Programar (lógica se mantiene)
    // ... (lógica para eventosProgramables se mantiene igual)
    const gruposFiltrados = grupos.filter(g => g.id == grupoId);
    let eventosProgramables = [];
    
    if (gruposFiltrados.length > 0 && gruposFiltrados[0].dias && gruposFiltrados[0].dias.length > 0) {
        const diasGrupo = gruposFiltrados[0].dias.split(',').map(d => {
            const dia = d.trim();
            const diasNormalizados = {
                'Lunes': 'Lunes', 'Martes': 'Martes', 'Miercoles': 'Miércoles', 
                'Jueves': 'Jueves', 'Viernes': 'Viernes', 'Sabado': 'Sábado', 'Domingo': 'Domingo'
            };
            return diasNormalizados[dia] || dia;
        });

        let currentDate = new Date(cleanInicio + 'T00:00:00');
        
        while (currentDate <= fechaFinObj) {
            const diaSemanaNombre = diasMap[currentDate.getDay()];
            const dateStr = currentDate.toISOString().split('T')[0];
            
            if (currentDate >= fechaInicioObj && currentDate <= fechaFinObj) {
                if (diasGrupo.includes(diaSemanaNombre)) {
                    if (!conflictDates.has(dateStr)) { 
                        const endDate = new Date(currentDate);
                        endDate.setDate(endDate.getDate() + 1);
                        
                        eventosProgramables.push({
                            title: `✅`,
                            start: dateStr,
                            end: endDate.toISOString().split('T')[0],
                            allDay: true,
                            classNames: ['fc-event-programar'], 
                            eventType: 'programar' 
                        });
                    }
                }
            }
            currentDate.setDate(currentDate.getDate() + 1);
        }
    }
    
    const eventos = [...eventosAjustados, ...eventosProgramables]; 
    
    // 3. CONFIGURACIÓN DEL CALENDARIO
    
    const start = new Date(cleanInicio + 'T00:00:00'); 
    const end = new Date(cleanFin + 'T00:00:00');     
    const diffTime = Math.abs(end - start);
    const midDate = new Date(start.getTime() + diffTime / 2);
    
    // =======================================================
    // CORRECCIÓN CLAVE: Ajustar validRange para ver el mes completo
    // =======================================================
    
    // Calcular el inicio del rango al primer día del mes de inicio (ej: 2025-10-01)
    const startOfMonth = new Date(fechaInicioObj.getFullYear(), fechaInicioObj.getMonth(), 1);
    
    // Calcular el final del rango al primer día del mes siguiente al mes de fin (ej: 2025-12-01)
    const endOfMonthExclusive = new Date(fechaFinObj.getFullYear(), fechaFinObj.getMonth(), 1);
    endOfMonthExclusive.setMonth(endOfMonthExclusive.getMonth() + 1); 

    // Restringir el rango de navegación a meses completos
    const validRange = {
        start: startOfMonth.toISOString().split('T')[0], 
        end: endOfMonthExclusive.toISOString().split('T')[0] 
    };

    const calendar = new FullCalendar.Calendar(miniCalendarEl, {
        initialView: 'dayGridMonth', 
        initialDate: fechaInicioObj,
        locale: 'es',
        height: 'auto',
        
        firstDay: 1, 
        
        fixedWeekCount: false, 
        showNonCurrentDates: false, 
        
        events: eventos,
        
        // Usamos el rango de mes completo para evitar el recorte
        validRange: validRange, 
        
        headerToolbar: {
            left: 'prev', 
            center: 'title',
            right: 'next' 
        },
        
        eventDisplay: 'block',
        
        eventDidMount: function(info) {
            info.el.style.fontSize = '14px';
            info.el.style.textAlign = 'center';
            info.el.style.padding = '1px';
            
            // Asignar color según el tipo de evento
            if (info.event.extendedProps.eventType === 'programado') {
                info.el.style.backgroundColor = '#fb7005ff'; // Rojo (Conflicto)
                info.el.style.borderColor = '#fb7005ff';
            } else if (info.event.extendedProps.eventType === 'programar') {
                 // Estilo para 'Programar'
                 info.el.style.backgroundColor = '#17a2b8'; // Azul (o tu color deseado)
                 info.el.style.borderColor = '#17a2b8';
            }
        },
        eventContent: function(arg) {
            return { html: `<div style="padding: 1px 2px;">${arg.event.title}</div>` };
        },
        dayCellContent: function(arg) {
             return arg.dayNumberText;
        }
    });

    // -------------------------------------------------------------------
    // Lógica de despliegue (se mantiene igual)
    // -------------------------------------------------------------------
    
    $(collapseEl).off('shown.bs.collapse').on('shown.bs.collapse', function () {
        if (!calendar.rendered) {
            calendar.render();
        }
        calendar.updateSize(); 
    });

    $(`#${calendarContainerId}`).slideDown(400); 
}

function validarYMostrarGrupos() {
    const turnoId = $('#turno_id').val();
    const fechaInicio = $('#fecha_inicio').val();
    const fechaFin = $('#fecha_fin').val();

    // Validaciones básicas
    if (!turnoId) {
        Swal.fire('Error', 'Debe seleccionar un turno', 'error');
        return;
    }

    if (!fechaInicio || !fechaFin) {
        Swal.fire('Error', 'Debe seleccionar las fechas de inicio y fin', 'error');
        return;
    }

    if (new Date(fechaInicio) > new Date(fechaFin)) {
        Swal.fire('Error', 'La fecha de inicio no puede ser mayor que la fecha de fin', 'error');
        return;
    }

    // Verificar que hay grupos cargados
    if ($('.grupo-card').length === 0) {
        Swal.fire('Error', 'No hay grupos de personal para validar', 'error');
        return;
    }

    // Mostrar loading
    Swal.fire({
        title: 'Validando disponibilidad...',
        text: 'Verificando contratos, vacaciones y programaciones existentes',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Validar disponibilidad del personal
    validarDisponibilidadPersonal(fechaInicio, fechaFin);
}

/**
 * CALCULA el número total de días de trabajo de un grupo en un rango de fechas usando JS nativo.
 * @param {string} diasStr - Cadena de días separados por coma (ej: "Lunes, Martes").
 * @param {string} startStr - Fecha de inicio ('YYYY-MM-DD').
 * @param {string} endStr - Fecha de fin ('YYYY-MM-DD').
 * @returns {number} Número de días laborables.
 */
function calcularDiasDelGrupoEnRango(diasStr, startStr, endStr) {
    if (!diasStr || !startStr || !endStr) return 0;

    // 1. Normalizar y preparar los días del grupo (ej: ['Lunes', 'Martes'])
    const diasTrabajo = diasStr.split(',').map(d => d.trim().toLowerCase()
        .replace('miércoles', 'miercoles') // Normalizar para usar el número de día
        .replace('sábado', 'sabado')
    );

    // Mapeo de Date.getDay() (0=Dom, 1=Lun, ..., 6=Sab) a nombres en español
    const dayNames = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

    let totalDias = 0;
    
    // Convertir fechas de inicio y fin (usando T00:00:00 para evitar problemas de zona horaria)
    let currentDate = new Date(startStr + 'T00:00:00');
    const end = new Date(endStr + 'T00:00:00');

    // 2. Iterar sobre cada día del rango
    while (currentDate <= end) {
        const dayOfWeek = dayNames[currentDate.getDay()]; // Obtiene el nombre del día
        
        // 3. Contar si el día actual está en la lista de días de trabajo del grupo
        if (diasTrabajo.includes(dayOfWeek)) {
            totalDias++;
        }
        
        // Avanzar al siguiente día
        currentDate.setDate(currentDate.getDate() + 1);
    }
    
    return totalDias;
}


function validarDisponibilidadPersonal(fechaInicio, fechaFin) {
    
    console.log('--- INICIO DE VALIDACIÓN (validarDisponibilidadPersonal) ---');

    let personalIds = [];
    let gruposData = []; // ALMACENA ASIGNACIONES ACTUALES DEL FRONTEND
    let erroresSeleccion = [];
    let grupoIdByArrayIndex = []; 

    $('.grupo-card').each(function(index) {
        const grupoId = $(this).find('input[name*="[grupo_id]"]').val();
        // Obtener IDs como STRING, luego parsear a INT si son válidos
        const conductorId = $(this).find('.select-conductor').val();
        const ayudante1Id = $(this).find('.select-ayudante1').val();
        const ayudante2Id = $(this).find('.select-ayudante2').val();
        
        // 1. Validar y recopilar personalIds (lista única)
        if (!conductorId) {
            erroresSeleccion.push(`Grupo #${index + 1}: Debe seleccionar un conductor`);
        } else {
            personalIds.push(parseInt(conductorId));
        }
        if (ayudante1Id) {
            personalIds.push(parseInt(ayudante1Id));
        }
        if (ayudante2Id) {
            personalIds.push(parseInt(ayudante2Id));
        }

        // 2. Crear la estructura de asignaciones actual (CLAVE)
        if (grupoId) {
            gruposData.push({
                grupo_id: parseInt(grupoId),
                // Almacenar las asignaciones para la verificación estricta posterior
                conductor_id: conductorId ? parseInt(conductorId) : null,
                ayudante1_id: ayudante1Id ? parseInt(ayudante1Id) : null,
                ayudante2_id: ayudante2Id ? parseInt(ayudante2Id) : null,
            });
            
            grupoIdByArrayIndex[index] = grupoId; 
        }
    });

    if (erroresSeleccion.length > 0) {
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Campos incompletos',
            html: erroresSeleccion.join('<br>'),
        });
        return;
    }

    personalIds = [...new Set(personalIds)];

    const payloadData = {
        _token: '{{ csrf_token() }}',
        personal_ids: personalIds,
        grupos_data: gruposData,
        turno_id: $('#turno_id').val(),
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin
    };
    
    console.log('📦 PAYLOAD COMPLETO ENVIADO AL SERVIDOR:', payloadData);
    console.log('Grupo ID por Posición de Array:', grupoIdByArrayIndex);


    $.ajax({
        url: '{{ route("programaciones.masiva.validar") }}',
        method: 'POST',
        data: payloadData,
        success: function(response) {
            Swal.close();
            
            // 🛑 LIMPIEZA AGRESIVA
            $('.grupo-card').each(function() {
                $(this).find('.validacion-container').remove(); 
                $(this).find('.alert-success, .alert-danger, .alert-warning').remove();
            });
            
            if (response.success) {
                validacionCompletada = true;
                $('#btnRegistrarProgramacion').prop('disabled', false);
                // ... (Mostrar mensajes de éxito) ...
                $('.grupo-card').each(function() {
                    const container = $('<div class="validacion-container mt-3"></div>');
                    container.html(`
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>
                            <strong>Validación exitosa</strong> - No se encontraron errores
                        </div>
                    `);
                    $(this).append(container);
                });
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Validación exitosa!',
                    text: 'No se encontraron errores. Puede proceder a registrar la programación.',
                    confirmButtonText: 'Entendido'
                });

            } else {
                validacionCompletada = false;
                // $('#btnRegistrarProgramacion').prop('disabled', true);
                
                let tieneErrores = false;
                let tieneErroresGraves = false;
                let conflictosPorGrupo = {};                
                
                // --- FUNCIÓN DE FILTRADO REUTILIZABLE ---
                const filtrarErroresPorAsignacion = (errores) => {
                    return errores.filter(p => {
                        const arrayIndex = parseInt(p.grupo_index);
                        const groupAssignments = gruposData[arrayIndex]; // Asignación actual del grupo
                        const personId = parseInt(p.id); // ID de la persona con conflicto
                        
                        if (!groupAssignments) return false;
                        
                        // Retorna TRUE solo si la persona con conflicto (personId) está realmente asignada
                        // en la tarjeta del formulario (groupAssignments) en el momento de la validación.
                        return (
                            personId === groupAssignments.conductor_id ||
                            personId === groupAssignments.ayudante1_id ||
                            personId === groupAssignments.ayudante2_id
                        );
                    });
                };

                // --- ERRORES: PERSONAL SIN CONTRATO ---
                if (response.errores.sin_contrato && response.errores.sin_contrato.length > 0) {
                    tieneErrores = true;
                    
                    // APLICAR FILTRADO ESTRICTO
                    const erroresSinContratoFiltrados = filtrarErroresPorAsignacion(response.errores.sin_contrato);
                    
                    erroresSinContratoFiltrados.forEach(p => {
                        const arrayIndex = parseInt(p.grupo_index);
                        const dbGrupoId = grupoIdByArrayIndex[arrayIndex];
                        
                        if (dbGrupoId) {
                            const cardIndex = $('.grupo-card').get().findIndex(el => {
                                return String($(el).find('input[name*="[grupo_id]"]').val()) === String(dbGrupoId);
                            });
                            
                            if (cardIndex !== -1) {
                                tieneErroresGraves = true;
                                agregarValidacionAGrupo(cardIndex, 'error', 'Personal sin contrato vigente', p.nombre);
                            }
                        }
                    });
                }
                
                // --- ERRORES: PERSONAL CON VACACIONES ---
                if (response.errores.con_vacaciones && response.errores.con_vacaciones.length > 0) {
                    tieneErrores = true;
                    

                    // APLICAR FILTRADO ESTRICTO
                    const erroresConVacacionesFiltrados = filtrarErroresPorAsignacion(response.errores.con_vacaciones);

                    erroresConVacacionesFiltrados.forEach(p => {
                        const arrayIndex = parseInt(p.grupo_index);
                        const dbGrupoId = grupoIdByArrayIndex[arrayIndex];
                        
                        if (dbGrupoId) {
                            const cardIndex = $('.grupo-card').get().findIndex(el => {
                                return String($(el).find('input[name*="[grupo_id]"]').val()) === String(dbGrupoId);
                            });

                            if (cardIndex !== -1) {
                                tieneErroresGraves = true;
                                agregarValidacionAGrupo(cardIndex, 'warning', 'Personal con vacaciones', `${p.nombre} (${p.fecha_inicio} al ${p.fecha_fin})`);
                            }
                        }
                    });
                }
                
                // --- ERRORES: CONFLICTOS DE PROGRAMACIÓN (Sin cambios) ---
                if (response.errores.con_programaciones && response.errores.con_programaciones.length > 0) {
                    tieneErrores = true;
                    // ... (Lógica de conflictos que ya funciona) ...
                    response.errores.con_programaciones.forEach(p => {
                        const evento = {
                            title: `📅`,
                            start: p.fecha_inicio,
                            end: p.fecha_fin,
                            allDay: true
                        };

                        if (!conflictosPorGrupo[p.grupo_id]) {
                            conflictosPorGrupo[p.grupo_id] = [];
                        }
                        conflictosPorGrupo[p.grupo_id].push(evento); 
                    });
                    
                    const gruposFiltrados = grupos.filter(g => g.turno_id == $('#turno_id').val());

                    for (const grupoId in conflictosPorGrupo) {
                        const eventos = conflictosPorGrupo[grupoId];
                        const grupo = gruposFiltrados.find(g => g.id == grupoId);

                        if (grupo) {
                            const index = $('.grupo-card').get().findIndex(el => String($(el).find('input[name*="[grupo_id]"]').val()) === String(grupoId));
                            const cardElement = $(`.grupo-card:eq(${index})`); 

                            if (cardElement.length > 0) {
                                inicializarMiniCalendario(grupoId, fechaInicio, fechaFin, eventos);
                                const totalDiasTrabajo = calcularDiasDelGrupoEnRango(grupo.dias, fechaInicio, fechaFin);
                                const eventosOcupados = eventos.length;
                                const diasRestantes = totalDiasTrabajo - eventosOcupados;
                                
                                if (diasRestantes > 0) {
                                    const mensaje = `Se puede completar programaciones para ${diasRestantes} días en este rango de fechas.`;
                                    agregarValidacionAGrupo(index, 'warning', 'Disponibilidad parcial', mensaje);
                                } else {
                                    tieneErroresGraves = true;
                                    const mensaje = `Elimine este grupo de personal para proceder ya que no tiene días disponibles en este rango de fechas.`;
                                    agregarValidacionAGrupo(index, 'error', 'Sin disponibilidad', mensaje);
                                }
                            }
                        }
                    }
                }
                
                if (tieneErroresGraves) {
                    $('#btnRegistrarProgramacion').prop('disabled', true);

                    Swal.fire({
                        icon: 'error',
                        title: 'Errores de validación encontrados',
                        text: 'Revise los errores mostrados debajo de cada grupo',
                        confirmButtonText: 'Aceptar'
                    });

                } else {
                    // Si hay errores pero NO son graves (solo warnings), habilitamos el botón
                    $('#btnRegistrarProgramacion').prop('disabled', false); 
                }
                
                
            }
        },
        error: function(xhr) {
            Swal.close();
            // ... (Manejo de errores AJAX) ...
            let errorMsg = 'Error al validar la disponibilidad del personal';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMsg
            });
        }
    });
}


function agregarValidacionAGrupo(grupoIndex, tipo, titulo, mensaje) {
    const grupos = $('.grupo-card');
    if (grupoIndex < grupos.length) {
        const grupo = $(grupos[grupoIndex]);
        
        // Si no existe el contenedor de validaciones, crearlo
        if (grupo.find('.validacion-container').length === 0) {
            const container = $('<div class="validacion-container mt-3"></div>');
            grupo.append(container);
        }
        
        const container = grupo.find('.validacion-container');
        const alertClass = tipo === 'error' ? 'alert-danger' : 'alert-warning';
        const icon = tipo === 'error' ? 'fa-times-circle' : 'fa-exclamation-triangle';
        
        const validacionHtml = `
            <div class="alert ${alertClass}" role="alert">
                <i class="fas ${icon} mr-2"></i>
                <strong>${titulo}:</strong> ${mensaje}
            </div>
        `;
        
        container.append(validacionHtml);
    }
}


function cargarGruposPersonal() {
    const container = $('#grupos-container');
    container.empty();

    // Filtrar grupos por el turno seleccionado
    const gruposFiltrados = grupos.filter(g => g.turno_id == turnoSeleccionado.id);
    
    // Debug: ver los datos de los grupos
    console.log('Grupos filtrados:', gruposFiltrados);

    if (gruposFiltrados.length === 0) {
        container.html(`
            <div class="alert alert-info text-center" role="alert">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>No hay grupos de personal asignados para este turno</strong>
            </div>
        `);
        $('#btnRegistrarProgramacion').prop('disabled', false);
        return;
    }

    // --- INICIO DE CAMBIOS CLAVE ---
    let row; // Variable para mantener la fila actual
    
    gruposFiltrados.forEach((grupo, index) => {
        // Iniciar una nueva fila por cada 3 grupos (o al inicio)
        if (index % 3 === 0) {
            // Crear un nuevo div con clase 'row'
            row = $('<div class="row"></div>');
            container.append(row); // Agregar la nueva fila al contenedor principal
        }
        
        console.log(`Grupo ${index + 1}:`, grupo); // Debug
        const zona = zonas.find(z => z.id == grupo.zona_id);
        const card = crearTarjetaGrupo(grupo, zona, index);
        
        // Envolver la tarjeta con la columna de Bootstrap (col-md-4 para 3 por fila)
        const col = $('<div class="col-md-4"></div>').append(card);
        
        // Agregar la columna a la fila actual
        row.append(col);
    });
    // --- FIN DE CAMBIOS CLAVE ---

    // Inicializar Select2 en todos los selects
    $('.select-vehiculo, .select-conductor, .select-ayudante1, .select-ayudante2').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Seleccione una opción',
        allowClear: true,
        language: {
            noResults: function() {
                return "No se encontraron resultados";
            },
            searching: function() {
                return "Buscando...";
            }
        }
    });
    
    // Resetear validación y deshabilitar botón de registrar
    validacionCompletada = false;
    $('#btnRegistrarProgramacion').prop('disabled', true);
}


function crearTarjetaGrupo(grupo, zona, index) {
    const dias = grupo.dias ? grupo.dias.split(',').map(d => d.trim()).join(', ') : 'No especificado';
    
    const card = $(`
        <div class="grupo-card">
            <div class="grupo-card-header">
    <span class="mb-0">
        <i class="fas fa-users mr-2"></i> GRUPO DE PERSONAL #${index + 1}
    </span>
    
    <button type="button" class="btn btn-danger btn-sm btn-eliminar-grupo" data-grupo-index="${index}" title="Eliminar este grupo">
        <i class="fas fa-trash"></i> 
    </button>
</div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="info-badge">
                        <strong><i class="fas fa-map-marker-alt mr-1"></i> Zona:</strong> ${zona ? zona.nombre : 'N/A'}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-badge">
                        <strong><i class="fas fa-clock mr-1"></i> Turno:</strong> ${turnoSeleccionado.name}
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="info-badge">
                        <strong><i class="fas fa-calendar-week mr-1"></i> Días:</strong> ${dias}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="font-weight-bold"><i class="fas fa-truck text-primary mr-2"></i>Vehículo: <span class="text-danger">*</span></label>
                    <select class="form-control select-vehiculo" name="grupos[${index}][vehiculo_id]" required>
                        <option value="">Seleccione un vehículo</option>
                        ${vehiculos.map(v => `<option value="${v.id}" ${grupo.vehiculo_id == v.id ? 'selected' : ''}>${v.codigo} - ${v.placa} (${v.marca ? v.marca.nombre : 'Sin marca'})</option>`).join('')}
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="font-weight-bold"><i class="fas fa-user-tie text-success mr-2"></i>Conductor: <span class="text-danger">*</span></label>
                    <select class="form-control select-conductor" name="grupos[${index}][conductor_id]" required>
                        <option value="">Seleccione un conductor</option>
                        ${conductores.map(c => `<option value="${c.id}" ${grupo.conductor_id == c.id ? 'selected' : ''}>${c.nombre_completo}</option>`).join('')}
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold"><i class="fas fa-user text-info mr-2"></i>Ayudante 1:</label>
                    <select class="form-control select-ayudante1" name="grupos[${index}][ayudante1_id]">
                        <option value="">Seleccione ayudante 1</option>
                        ${ayudantes.map(a => `<option value="${a.id}" ${grupo.ayudante1_id == a.id ? 'selected' : ''}>${a.nombre_completo}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold"><i class="fas fa-user text-info mr-2"></i>Ayudante 2:</label>
                    <select class="form-control select-ayudante2" name="grupos[${index}][ayudante2_id]">
                        <option value="">Seleccione ayudante 2</option>
                        ${ayudantes.map(a => `<option value="${a.id}" ${grupo.ayudante2_id == a.id ? 'selected' : ''}>${a.nombre_completo}</option>`).join('')}
                    </select>
                </div>
            </div>

      <div id="calendar-conflict-group-${grupo.id}" class="card card-danger card-outline mt-3" style="display: none;">
        
        <div class="card-header p-0" id="header-group-${grupo.id}">
          <a class="d-block w-100 text-left collapsed" data-toggle="collapse" href="#collapse-calendar-group-${grupo.id}" role="button" aria-expanded="false" aria-controls="collapse-calendar-group-${grupo.id}" style="padding: 1rem 1.25rem; text-decoration: none; color: white; background-color: #dc3545; border-radius: 0.25rem 0.25rem 0 0;">
            <div class="d-flex justify-content-between align-items-center">
                <span class="mr-3"> <strong> ⚠️ Superposición de programación: </strong> Existen programaciones para el mismo turno, zona y vehículo en el rango de fechas. </span>
              <i class="fas fa-chevron-down toggle-icon"></i>
            </div>
          </a>
        </div>
        
        <div id="collapse-calendar-group-${grupo.id}" class="collapse" aria-labelledby="header-group-${grupo.id}">
          <div class="card-body p-2">
            <div id="mini-calendar-${grupo.id}"></div>
          </div>
        </div>
      </div>

            <input type="hidden" name="grupos[${index}][grupo_id]" value="${grupo.id}">
            <input type="hidden" name="grupos[${index}][zona_id]" value="${grupo.zona_id}">
        </div>
    `);

    return card;
}

function guardarProgramacionMasiva() {
    // Verificar que se haya completado la validación
   

    // Validar que se hayan seleccionado al menos conductor y vehículo en cada grupo
    let valido = true;
    let errores = [];

    $('.grupo-card').each(function(index) {
        const vehiculo = $(this).find('.select-vehiculo').val();
        const conductor = $(this).find('.select-conductor').val();

        if (!vehiculo) {
            errores.push(`Grupo #${index + 1}: Debe seleccionar un vehículo`);
            valido = false;
        }

        if (!conductor) {
            errores.push(`Grupo #${index + 1}: Debe seleccionar un conductor`);
            valido = false;
        }
    });

    if (!valido) {
        Swal.fire({
            icon: 'error',
            title: 'Campos incompletos',
            html: errores.join('<br>'),
        });
        return;
    }

    // Confirmación
    Swal.fire({
        title: '¿Confirmar registro?',
        text: 'Se registrarán las programaciones para todos los grupos',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, registrar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Guardando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Preparar datos en el formato correcto
            const programaciones = [];
            $('.grupo-card').each(function(index) {
                const grupoId = $(this).find('input[name*="[grupo_id]"]').val();
                const zonaId = $(this).find('input[name*="[zona_id]"]').val();
                const vehiculoId = $(this).find('.select-vehiculo').val();
                const conductorId = $(this).find('.select-conductor').val();
                const ayudante1Id = $(this).find('.select-ayudante1').val();
                const ayudante2Id = $(this).find('.select-ayudante2').val();

                programaciones.push({
                    grupo_id: grupoId,
                    zona_id: zonaId,
                    vehiculo_id: vehiculoId,
                    conductor_id: conductorId,
                    ayudante1_id: ayudante1Id || null,
                    ayudante2_id: ayudante2Id || null
                });
            });

            // Obtener los días del grupo (asumiendo que todos tienen los mismos días)
            const gruposFiltrados = grupos.filter(g => g.turno_id == turnoSeleccionado.id);
            let dias = [];
            if (gruposFiltrados.length > 0 && gruposFiltrados[0].dias) {
                dias = gruposFiltrados[0].dias.split(',').map(d => {
                    // Normalizar días: agregar tildes donde corresponda
                    const dia = d.trim();
                    const diasNormalizados = {
                        'Miercoles': 'Miércoles',
                        'Sabado': 'Sábado'
                    };
                    return diasNormalizados[dia] || dia;
                });
            }

            const datosEnviar = {
                _token: '{{ csrf_token() }}',
                turno_id: $('#turno_id').val(),
                fecha_inicio: $('#fecha_inicio').val(),
                fecha_fin: $('#fecha_fin').val(),
                dias: dias,
                programaciones: programaciones
            };

            console.log('Datos a enviar:', datosEnviar); // Debug

            // Enviar datos al servidor
            $.ajax({
                url: '{{ route("programaciones.masiva.store") }}',
                method: 'POST',
                data: datosEnviar,
                success: function(response) {
                    Swal.close();
                    
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message || 'Programaciones registradas correctamente',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            window.location.href = '{{ route("programaciones.index") }}';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'No se pudieron registrar las programaciones'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    
                    let errorMsg = 'Error al guardar las programaciones';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errorMsg
                    });
                }
            });
        }
    });
}
</script>
@endpush