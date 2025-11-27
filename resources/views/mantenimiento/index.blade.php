@extends('layouts.app')

@section('title', 'Gestión de Mantenimiento')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Mantenimiento</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Mantenimientos</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevoMantenimiento">
                        <i class="fas fa-plus"></i> Nuevo Mantenimiento
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="tablaMantenimientos" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Horarios</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Modal Mantenimiento -->
<div class="modal fade" id="modalMantenimiento" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalMantenimientoTitle">Nuevo Mantenimiento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formMantenimiento">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="mantenimiento_id" name="mantenimiento_id">
                    
                    <div class="form-group">
                        <label for="nombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_inicio">Fecha Inicio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_fin">Fecha Fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Horarios -->
<div class="modal fade" id="modalHorarios" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHorariosTitle">Horarios de Mantenimiento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="horarios_mantenimiento_id">
                
                <div class="mb-3">
                    <button type="button" class="btn btn-success btn-sm" id="btnNuevoHorario">
                        <i class="fas fa-plus"></i> Agregar Horario
                    </button>
                </div>

                <table id="tablaHorarios" class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Día</th>
                            <th>Vehículo</th>
                            <th>Responsable</th>
                            <th>Tipo</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Días Gen.</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Horario (Crear/Editar) -->
<div class="modal fade" id="modalHorario" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHorarioTitle">Nuevo Horario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formHorario">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="horario_id" name="horario_id">
                    <input type="hidden" id="horario_mantenimiento_id" name="mantenimiento_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vehiculo_id">Vehículo <span class="text-danger">*</span></label>
                                <select class="form-control" id="vehiculo_id" name="vehiculo_id" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($vehiculos as $vehiculo)
                                        <option value="{{ $vehiculo->id }}">
                                            {{ $vehiculo->placa }}
                                            @if($vehiculo->marca && $vehiculo->modelo)
                                                - {{ $vehiculo->marca->nombre }} {{ $vehiculo->modelo->nombre }}
                                            @elseif($vehiculo->nombre)
                                                - {{ $vehiculo->nombre }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="responsable_id">Responsable <span class="text-danger">*</span></label>
                                <select class="form-control" id="responsable_id" name="responsable_id" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($responsables as $responsable)
                                        <option value="{{ $responsable->id }}">{{ $responsable->nombres }} {{ $responsable->apellido_paterno }} {{ $responsable->apellido_materno }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_mantenimiento">Tipo de Mantenimiento <span class="text-danger">*</span></label>
                                <select class="form-control" id="tipo_mantenimiento" name="tipo_mantenimiento" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Preventivo">Preventivo</option>
                                    <option value="Limpieza">Limpieza</option>
                                    <option value="Reparación">Reparación</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dia_semana">Día de la Semana <span class="text-danger">*</span></label>
                                <select class="form-control" id="dia_semana" name="dia_semana" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Lunes">Lunes</option>
                                    <option value="Martes">Martes</option>
                                    <option value="Miércoles">Miércoles</option>
                                    <option value="Jueves">Jueves</option>
                                    <option value="Viernes">Viernes</option>
                                    <option value="Sábado">Sábado</option>
                                    <option value="Domingo">Domingo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hora_inicio">Hora Inicio <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hora_fin">Hora Fin <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Días -->
<div class="modal fade" id="modalDias" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDiasTitle">Días Generados</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="infoDias" class="alert alert-info"></div>
                
                <table id="tablaDias" class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Observación</th>
                            <th>Imagen</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Día (Editar) -->
<div class="modal fade" id="modalDia" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Día</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formDia" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="dia_id" name="dia_id">
                    
                    <div class="form-group">
                        <label for="dia_fecha">Fecha</label>
                        <input type="text" class="form-control" id="dia_fecha" readonly>
                    </div>

                    <div class="form-group">
                        <label for="observacion">Observación</label>
                        <textarea class="form-control" id="observacion" name="observacion" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="imagen">Imagen</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="imagen" name="imagen" accept="image/*">
                            <label class="custom-file-label" for="imagen">Seleccionar archivo...</label>
                        </div>
                        <small class="form-text text-muted">Formatos: JPG, PNG. Máximo 2MB.</small>
                        <div id="imagenPreview" class="mt-2"></div>
                    </div>

                    <div class="form-group">
                        <label>Estado del Mantenimiento</label>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="realizado_si" name="realizado" value="1" class="custom-control-input">
                            <label class="custom-control-label" for="realizado_si">Realizado</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="realizado_no" name="realizado" value="0" class="custom-control-input">
                            <label class="custom-control-label" for="realizado_no">No Realizado</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
<!-- Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/es.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    console.log('Script iniciado');
    
    // Verificar librerías
    if (typeof $ === 'undefined') {
        console.error('jQuery no está cargado');
        return;
    }
    if (typeof moment === 'undefined') {
        console.error('Moment.js no está cargado');
        return;
    }
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 no está cargado');
        return;
    }
    if (!$.fn.DataTable) {
        console.error('DataTables no está cargado');
        return;
    }
    
    console.log('Todas las librerías están cargadas correctamente');
    
    let tablaMantenimientos, tablaHorarios, tablaDias;

    // ==========================================
    // INICIALIZACIÓN
    // ==========================================
    
    inicializarTablaMantenimientos();

    // ==========================================
    // TABLA MANTENIMIENTOS
    // ==========================================
    
    function inicializarTablaMantenimientos() {
        console.log('Inicializando tabla mantenimientos');
        
        tablaMantenimientos = $('#tablaMantenimientos').DataTable({
            processing: true,
            ajax: {
                url: '{{ route("admin.mantenimiento.index") }}',
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                dataSrc: function(json) {
                    console.log('Datos recibidos:', json);
                    return json;
                },
                error: function(xhr, error, thrown) {
                    console.error('Error al cargar datos:', error, thrown);
                    console.error('Respuesta:', xhr.responseText);
                }
            },
            columns: [
                { data: 'nombre' },
                { 
                    data: 'fecha_inicio',
                    render: function(data) {
                        return moment(data).format('DD/MM/YYYY');
                    }
                },
                { 
                    data: 'fecha_fin',
                    render: function(data) {
                        return moment(data).format('DD/MM/YYYY');
                    }
                },
                { 
                    data: null,
                    render: function(data) {
                        const cantidadHorarios = data.horarios ? data.horarios.length : 0;
                        return `<button class="btn btn-info btn-sm btnVerHorarios" data-id="${data.id}" data-nombre="${data.nombre}">
                                    <i class="fas fa-calendar-alt"></i> Ver (${cantidadHorarios})
                                </button>`;
                    }
                },
                { 
                    data: null,
                    orderable: false,
                    render: function(data) {
                        return `
                            <button class="btn btn-warning btn-sm btnEditarMantenimiento" data-id="${data.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btnEliminarMantenimiento" data-id="${data.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            }
        });
    }

    // ==========================================
    // CRUD MANTENIMIENTOS
    // ==========================================
    
    // Nuevo Mantenimiento
    $('#btnNuevoMantenimiento').click(function() {
        console.log('Botón Nuevo Mantenimiento clickeado');
        $('#formMantenimiento')[0].reset();
        $('#mantenimiento_id').val('');
        $('#modalMantenimientoTitle').text('Nuevo Mantenimiento');
        $('#modalMantenimiento').modal('show');
    });

    // Editar Mantenimiento
    $(document).on('click', '.btnEditarMantenimiento', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: '{{ route("admin.mantenimiento.index") }}',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                const mantenimiento = response.find(m => m.id == id);
                
                $('#mantenimiento_id').val(mantenimiento.id);
                $('#nombre').val(mantenimiento.nombre);
                $('#fecha_inicio').val(mantenimiento.fecha_inicio);
                $('#fecha_fin').val(mantenimiento.fecha_fin);
                
                $('#modalMantenimientoTitle').text('Editar Mantenimiento');
                $('#modalMantenimiento').modal('show');
            }
        });
    });

    // Guardar Mantenimiento
    $('#formMantenimiento').submit(function(e) {
        e.preventDefault();
        
        const id = $('#mantenimiento_id').val();
        const url = id ? `/mantenimiento/${id}` : '/mantenimiento';
        const method = id ? 'PUT' : 'POST';
        
        let formData = $(this).serialize();
        if (method === 'PUT') {
            formData += '&_method=PUT';
        }
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#modalMantenimiento').modal('hide');
                tablaMantenimientos.ajax.reload();
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ocurrió un error al guardar'
                });
            }
        });
    });

    // Eliminar Mantenimiento
    $(document).on('click', '.btnEliminarMantenimiento', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción no se puede revertir",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/mantenimiento/${id}`,
                    type: 'POST',
                    data: { 
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        tablaMantenimientos.ajax.reload();
                        
                        Swal.fire({
                            icon: 'success',
                            title: '¡Eliminado!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'No se pudo eliminar'
                        });
                    }
                });
            }
        });
    });

    // ==========================================
    // HORARIOS
    // ==========================================
    
    // Ver Horarios
    $(document).on('click', '.btnVerHorarios', function() {
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');
        
        $('#horarios_mantenimiento_id').val(id);
        $('#modalHorariosTitle').text(`Horarios: ${nombre}`);
        
        cargarHorarios(id);
        $('#modalHorarios').modal('show');
    });

    function cargarHorarios(mantenimientoId) {
        if ($.fn.DataTable.isDataTable('#tablaHorarios')) {
            $('#tablaHorarios').DataTable().destroy();
        }

        tablaHorarios = $('#tablaHorarios').DataTable({
            processing: true,
            ajax: {
                url: '{{ route("admin.mantenimiento.index") }}',
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                dataSrc: function(json) {
                    const mantenimiento = json.find(m => m.id == mantenimientoId);
                    return mantenimiento ? mantenimiento.horarios : [];
                }
            },
            columns: [
                { data: 'dia_semana' },
                { 
                    data: 'vehiculo',
                    render: function(data) {
                        return data ? `${data.placa}` : 'N/A';
                    }
                },
                { 
                    data: 'responsable',
                    render: function(data) {
                        if (!data) return 'N/A';
                        const nombre = data.nombres || '';
                        const apellidoP = data.apellido_paterno || '';
                        const apellidoM = data.apellido_materno || '';
                        return `${nombre} ${apellidoP} ${apellidoM}`.trim();
                    }
                },
                { data: 'tipo_mantenimiento' },
                { 
                    data: 'hora_inicio',
                    render: function(data) {
                        return moment(data, 'HH:mm:ss').format('hh:mm A');
                    }
                },
                { 
                    data: 'hora_fin',
                    render: function(data) {
                        return moment(data, 'HH:mm:ss').format('hh:mm A');
                    }
                },
                { 
                    data: 'dias',
                    render: function(data) {
                        return data ? data.length : 0;
                    }
                },
                { 
                    data: null,
                    orderable: false,
                    render: function(data) {
                        return `
                            <button class="btn btn-info btn-sm btnVerDias" data-id="${data.id}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm btnEditarHorario" data-id="${data.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btnEliminarHorario" data-id="${data.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            paging: false,
            searching: false
        });
    }

    // Nuevo Horario
    $(document).on('click', '#btnNuevoHorario', function() {
        $('#formHorario')[0].reset();
        $('#horario_id').val('');
        $('#horario_mantenimiento_id').val($('#horarios_mantenimiento_id').val());
        $('#modalHorarioTitle').text('Nuevo Horario');
        $('#modalHorario').modal('show');
    });

    // Editar Horario
    $(document).on('click', '.btnEditarHorario', function() {
        const id = $(this).data('id');
        const mantenimientoId = $('#horarios_mantenimiento_id').val();
        
        $.ajax({
            url: '{{ route("admin.mantenimiento.index") }}',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                const mantenimiento = response.find(m => m.id == mantenimientoId);
                const horario = mantenimiento.horarios.find(h => h.id == id);
                
                $('#horario_id').val(horario.id);
                $('#horario_mantenimiento_id').val(mantenimiento.id);
                $('#vehiculo_id').val(horario.vehiculo_id);
                $('#responsable_id').val(horario.responsable_id);
                $('#tipo_mantenimiento').val(horario.tipo_mantenimiento);
                $('#dia_semana').val(horario.dia_semana);
                $('#hora_inicio').val(moment(horario.hora_inicio, 'HH:mm:ss').format('HH:mm'));
                $('#hora_fin').val(moment(horario.hora_fin, 'HH:mm:ss').format('HH:mm'));
                
                $('#modalHorarioTitle').text('Editar Horario');
                $('#modalHorario').modal('show');
            }
        });
    });

    // Guardar Horario
    $('#formHorario').submit(function(e) {
        e.preventDefault();
        
        const id = $('#horario_id').val();
        const url = id ? `/mantenimiento/horarios/${id}` : '/mantenimiento/horarios';
        const method = id ? 'PUT' : 'POST';
        
        let formData = $(this).serialize();
        if (method === 'PUT') {
            formData += '&_method=PUT';
        }
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#modalHorario').modal('hide');
                cargarHorarios($('#horarios_mantenimiento_id').val());
                
                // Recargar tabla principal para actualizar contador
                tablaMantenimientos.ajax.reload(null, false);
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ocurrió un error al guardar'
                });
            }
        });
    });

    // Eliminar Horario
    $(document).on('click', '.btnEliminarHorario', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: '¿Está seguro?',
            text: "También se eliminarán los días generados",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/mantenimiento/horarios/${id}`,
                    type: 'POST',
                    data: { 
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        cargarHorarios($('#horarios_mantenimiento_id').val());
                        
                        // Recargar tabla principal para actualizar contador
                        tablaMantenimientos.ajax.reload(null, false);
                        
                        Swal.fire({
                            icon: 'success',
                            title: '¡Eliminado!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'No se pudo eliminar'
                        });
                    }
                });
            }
        });
    });

    // ==========================================
    // DÍAS
    // ==========================================
    
    // Ver Días
    $(document).on('click', '.btnVerDias', function() {
        const horarioId = $(this).data('id');
        
        $.ajax({
            url: `/mantenimiento/horarios/${horarioId}/dias`,
            type: 'GET',
            success: function(response) {
                const horario = response.data.horario;
                const dias = response.data.dias;
                
                $('#modalDiasTitle').text(`Días: ${horario.dia_semana} - ${horario.vehiculo.placa}`);
                $('#infoDias').html(`
                    <strong>Vehículo:</strong> ${horario.vehiculo.placa}<br>
                    <strong>Responsable:</strong> ${horario.responsable.nombres} ${horario.responsable.apellido_paterno} ${horario.responsable.apellido_materno}<br>
                    <strong>Tipo:</strong> ${horario.tipo_mantenimiento}<br>
                    <strong>Horario:</strong> ${moment(horario.hora_inicio, 'HH:mm:ss').format('hh:mm A')} - ${moment(horario.hora_fin, 'HH:mm:ss').format('hh:mm A')}
                `);
                
                cargarDias(dias);
                $('#modalDias').modal('show');
            }
        });
    });

    function cargarDias(dias) {
        if ($.fn.DataTable.isDataTable('#tablaDias')) {
            $('#tablaDias').DataTable().destroy();
        }

        tablaDias = $('#tablaDias').DataTable({
            data: dias,
            columns: [
                { 
                    data: 'fecha',
                    render: function(data) {
                        return moment(data).format('DD/MM/YYYY');
                    }
                },
                { 
                    data: 'observacion',
                    render: function(data) {
                        return data || '<em class="text-muted">Sin observación</em>';
                    }
                },
                { 
                    data: 'imagen',
                    render: function(data) {
                        if (data) {
                            return `<a href="/storage/${data}" target="_blank">
                                        <i class="fas fa-image"></i> Ver
                                    </a>`;
                        }
                        return '<em class="text-muted">Sin imagen</em>';
                    }
                },
                { 
                    data: 'realizado',
                    render: function(data) {
                        if (data) {
                            return '<span class="badge badge-success"><i class="fas fa-check"></i> Realizado</span>';
                        }
                        return '<span class="badge badge-danger"><i class="fas fa-times"></i> Pendiente</span>';
                    }
                },
                { 
                    data: null,
                    orderable: false,
                    render: function(data) {
                        return `
                            <button class="btn btn-warning btn-sm btnEditarDia" data-id="${data.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                        `;
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            paging: false,
            searching: false
        });
    }

    // Editar Día
    $(document).on('click', '.btnEditarDia', function() {
        const diaData = tablaDias.row($(this).parents('tr')).data();
        
        $('#dia_id').val(diaData.id);
        $('#dia_fecha').val(moment(diaData.fecha).format('DD/MM/YYYY'));
        $('#observacion').val(diaData.observacion || '');
        
        // Radio buttons
        if (diaData.realizado) {
            $('#realizado_si').prop('checked', true);
        } else {
            $('#realizado_no').prop('checked', true);
        }
        
        // Mostrar imagen actual si existe
        if (diaData.imagen) {
            $('#imagenPreview').html(`
                <img src="/storage/${diaData.imagen}" class="img-thumbnail" style="max-width: 200px;">
                <p class="text-muted">Imagen actual</p>
            `);
        } else {
            $('#imagenPreview').html('');
        }
        
        $('#modalDia').modal('show');
    });

    // Actualizar label del input file
    $('#imagen').change(function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });

    // Guardar Día
    $('#formDia').submit(function(e) {
        e.preventDefault();
        
        const id = $('#dia_id').val();
        const formData = new FormData(this);
        formData.append('_method', 'PUT');
        formData.append('_token', '{{ csrf_token() }}');
        
        $.ajax({
            url: `/mantenimiento/dias/${id}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#modalDia').modal('hide');
                
                // Recargar la tabla de días
                const horarioId = response.data.horario_mantenimiento_id;
                $.ajax({
                    url: `/mantenimiento/horarios/${horarioId}/dias`,
                    type: 'GET',
                    success: function(resp) {
                        cargarDias(resp.data.dias);
                    }
                });
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ocurrió un error al guardar'
                });
            }
        });
    });

    // Limpiar formulario al cerrar modales
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0]?.reset();
    });
});
</script>
@endpush


