@extends('layouts.app')

@push('styles')
<style>
.badge-presente {
    background-color: #28a745;
    color: white;
}

.badge-ausente {
    background-color: #dc3545;
    color: white;
}

.badge-tardanza {
    background-color: #ffc107;
    color: #212529;
}

.badge-permiso {
    background-color: #17a2b8;
    color: white;
}
    
.badge-entrada-registrada {
    background-color: #3f6791; 
    color: white;
}

.badge-salida-registrada {
    background-color: #28a745;
    color: white;
}

.badge-salida-falta {
    background-color: #dc3545;
    color: white;
}

.select2-container .select2-selection--single {
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    height: 38px !important;
    display: flex;
    align-items: center;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #495057 !important;
    line-height: 36px !important;
}

.modal-header {
    background-color: #3f6791;
    color: #fff;
}

.modal-header .btn-close,
.modal-header .close {
    background: none;
    border: none;
    color: #fff !important;
    opacity: 1;
    font-size: 1.4rem;
}

.modal-header .btn-close:hover,
.modal-header .close:hover {
    color: #f8f9fa !important;
}

.form-row {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.form-row .form-group {
    flex: 1;
    margin-bottom: 0;
}

.btn-filtrar {
    background-color: #17a2b8;
    color: white;
    border: none;
}

.btn-filtrar:hover {
    background-color: #138496;
    color: white;
}
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Asistencias</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item">Gestión de Empleados</li>
                    <li class="breadcrumb-item active">Asistencia</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-check-circle"></i> Asistencias
                    </h3>
                    <div class="d-flex">
                        <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#modalAsistencia" id="btnNuevaAsistencia">
                            <i class="fas fa-plus"></i> Agregar Nueva Asistencia
                        </button>
                         <button type="button" class="btn btn-success" id="btnIrModulo">
                             <i class="fas fa-home"></i> Registrar en módulo
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <form id="formFiltros" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="fecha_inicio">Fecha de inicio: <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ request('fecha_inicio', date('Y-m-01')) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="fecha_fin">Fecha de fin:</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ request('fecha_fin') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="buscar">Buscar:</label>
                                <input type="text" class="form-control" id="buscar" name="buscar" placeholder="DNI, nombre, apellido..." value="{{ request('buscar') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-filtrar mr-2" style="flex: 1;">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                                <button type="button" class="btn btn-secondary" id="btnLimpiarFiltros" style="flex: 1;">
                                    <i class="fas fa-eraser"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tablaAsistencias">
                            <thead>
                                <tr>
                                    <th>DNI</th>
                                    <th>EMPLEADO</th>
                                    <th>FECHA</th>
                                    <th>TIPO</th>
                                    <th>ENTRADA</th> 
                                    <th>SALIDA</th> 
                                    <th>NOTAS</th>
                                    <th>ACCIÓN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($asistencias as $asistencia)
                                <tr>
                                    <td>{{ $asistencia->personal->dni }}</td>
                                    <td>{{ $asistencia->personal->nombres }} {{ $asistencia->personal->apellido_paterno }} {{ $asistencia->personal->apellido_materno }}</td>
                                    <td>{{ \Carbon\Carbon::parse($asistencia->fecha)->format('d/m/Y') }}</td>
                                    <td>{{ $asistencia->personal->funcion->nombre ?? 'Sin función' }}</td>
                                    
                                    <td>
                                        @php
                                            $estado_texto = '';
                                            $estado_clase = '';
                                            $hora_entrada_formato = $asistencia->hora_entrada ? \Carbon\Carbon::parse($asistencia->hora_entrada)->format('H:i') : '';

                                            if ($asistencia->estado == 'presente') {
                                                $estado_texto = 'Presente';
                                                $estado_clase = 'badge-presente';
                                            } elseif ($asistencia->estado == 'tardanza') {
                                                $estado_texto = 'Tardanza';
                                                $estado_clase = 'badge-tardanza';
                                            } elseif ($asistencia->estado == 'ausente') {
                                                $estado_texto = 'Ausente';
                                                $estado_clase = 'badge-ausente';
                                            } elseif ($asistencia->estado == 'permiso') {
                                                $estado_texto = 'Permiso';
                                                $estado_clase = 'badge-permiso';
                                            }
                                        @endphp

                                        @if ($asistencia->estado == 'presente' || $asistencia->estado == 'tardanza')
                                            <span class="badge {{ $estado_clase }}">{{ $estado_texto }} ({{ $hora_entrada_formato }})</span>
                                        @else
                                            <span class="badge {{ $estado_clase }}">{{ $estado_texto }}</span>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        @if ($asistencia->estado == 'permiso' || $asistencia->estado == 'ausente')
                                            -
                                        @elseif ($asistencia->hora_salida)
                                            <span class="badge badge-salida-registrada">Registrada ({{ \Carbon\Carbon::parse($asistencia->hora_salida)->format('H:i') }})</span>
                                        @else
                                            <span class="badge badge-salida-falta">Falta</span>
                                        @endif
                                    </td>
                                    
                                    <td>{{ $asistencia->observaciones ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info btnVerAsistencia" data-id="{{ $asistencia->id }}" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning btnEditarAsistencia" data-id="{{ $asistencia->id }}" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btnEliminarAsistencia" data-id="{{ $asistencia->id }}" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No hay registros de asistencia</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAsistencia" tabindex="-1" aria-labelledby="modalAsistenciaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAsistenciaLabel">Nueva Asistencia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAsistencia">
                <div class="modal-body">
                    <input type="hidden" id="asistencia_id" name="asistencia_id">
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="personal_id">Empleado <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="personal_id" name="personal_id" required>
                                <option value="">Seleccione un empleado</option>
                                @foreach($personal as $p)
                                <option value="{{ $p->id }}">{{ $p->dni }} - {{ $p->nombres }} {{ $p->apellido_paterno }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-text personal_id_error"></small>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="fecha">Fecha <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required>
                            <small class="text-danger error-text fecha_error"></small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="hora_entrada">Hora de Entrada</label>
                            <input type="time" class="form-control" id="hora_entrada" name="hora_entrada">
                            <small class="text-danger error-text hora_entrada_error"></small>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="hora_salida">Hora de Salida</label>
                            <input type="time" class="form-control" id="hora_salida" name="hora_salida">
                            <small class="text-danger error-text hora_salida_error"></small>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="estado">Estado <span class="text-danger">*</span></label>
                            <select class="form-control" id="estado" name="estado" required>
                                <option value="">Seleccione</option>
                                <option value="presente">Presente</option>
                                <option value="ausente">Ausente</option>
                                <option value="tardanza">Tardanza</option>
                                <option value="permiso">Permiso</option>
                            </select>
                            <small class="text-danger error-text estado_error"></small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="observaciones">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" maxlength="500"></textarea>
                        <small class="text-muted">Máximo 500 caracteres</small>
                        <small class="text-danger error-text observaciones_error"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarAsistencia">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVerAsistencia" tabindex="-1" aria-labelledby="modalVerAsistenciaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerAsistenciaLabel">Detalles de Asistencia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>DNI:</strong> <span id="ver_dni"></span></p>
                        <p><strong>Empleado:</strong> <span id="ver_empleado"></span></p>
                        <p><strong>Función:</strong> <span id="ver_funcion"></span></p>
                        <p><strong>Fecha:</strong> <span id="ver_fecha"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Hora Entrada:</strong> <span id="ver_hora_entrada"></span></p>
                        <p><strong>Hora Salida:</strong> <span id="ver_hora_salida"></span></p>
                        <p><strong>Estado:</strong> <span id="ver_estado"></span></p>
                        <p><strong>Observaciones:</strong> <span id="ver_observaciones"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2({
        dropdownParent: $('#modalAsistencia'),
        width: '100%'
    });

    let tabla = $('#tablaAsistencias').DataTable({
        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "sLoadingRecords": "Cargando...",
            "sPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        },
        pageLength: 10,
        order: [[2, 'desc']],
        columnDefs: [
            // Ahora la columna de acciones es la 7 (contando desde 0)
            { orderable: false, targets: 7 } 
        ]
    });

    $('#registrosPorPagina').on('change', function() {
        tabla.page.len($(this).val()).draw();
    });

    $('#btnIrModulo').on('click', function() {
        window.open("{{ route('asistencias.registrar') }}", '_blank');
    });

    $('#formFiltros').on('submit', function(e) {
        e.preventDefault();
        const params = new URLSearchParams();
        
        if ($('#fecha_inicio').val()) params.append('fecha_inicio', $('#fecha_inicio').val());
        if ($('#fecha_fin').val()) params.append('fecha_fin', $('#fecha_fin').val());
        if ($('#buscar').val()) params.append('buscar', $('#buscar').val());
        
        window.location.href = "{{ route('asistencias.index') }}" + (params.toString() ? '?' + params.toString() : '');
    });

    $('#btnLimpiarFiltros').on('click', function() {
        window.location.href = "{{ route('asistencias.index') }}";
    });

    function toggleHoraEntradaRequirement() {
        const estado = $('#estado').val();
        const $horaEntrada = $('#hora_entrada');
        const $label = $horaEntrada.siblings('label');
        
        $label.find('.text-danger').remove();

        if (estado === 'presente' || estado === 'tardanza') {
            $horaEntrada.prop('required', true);
            if ($label.html().indexOf('text-danger') === -1) {
                $label.append(' <span class="text-danger">*</span>');
            }
        } else {
            $horaEntrada.prop('required', false);
        }
    }
    
    toggleHoraEntradaRequirement(); 
    $('#estado').on('change', toggleHoraEntradaRequirement);

    $('#btnNuevaAsistencia').on('click', function() {
        $('#formAsistencia')[0].reset();
        $('#asistencia_id').val('');
        $('#modalAsistenciaLabel').text('Nueva Asistencia');
        $('#personal_id').val('').trigger('change');
        $('.error-text').text('');
        $('#fecha').val('{{ date("Y-m-d") }}');
        
        $('#estado').val('');
        toggleHoraEntradaRequirement(); 
    });

    $('#formAsistencia').on('submit', function(e) {
        e.preventDefault();
        
        $('.error-text').text('');
        const asistenciaId = $('#asistencia_id').val();
        const url = asistenciaId ? `/asistencias/${asistenciaId}` : '{{ route("asistencias.store") }}';
        const method = asistenciaId ? 'PUT' : 'POST';

        const estado = $('#estado').val();
        const horaEntrada = $('#hora_entrada').val();

        if ((estado === 'presente' || estado === 'tardanza') && !horaEntrada) {
            $('.hora_entrada_error').text('La hora de entrada es requerida para el estado de Presente o Tardanza.');
            return;
        }
        $('.hora_entrada_error').text('');

        const formData = {
            personal_id: $('#personal_id').val(),
            fecha: $('#fecha').val(),
            hora_entrada: $('#hora_entrada').val() || null, 
            hora_salida: $('#hora_salida').val() || null,
            estado: $('#estado').val(),
            observaciones: $('#observaciones').val()
        };

        $.ajax({
            url: url,
            method: method,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $(`.${key}_error`).text(value[0]);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Error al procesar la solicitud'
                    });
                }
            }
        });
    });

    $('.btnVerAsistencia').on('click', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/asistencias/${id}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const a = response.asistencia;
                    $('#ver_dni').text(a.personal.dni);
                    $('#ver_empleado').text(`${a.personal.nombres} ${a.personal.apellido_paterno} ${a.personal.apellido_materno}`);
                    $('#ver_funcion').text(a.personal.funcion?.nombre || 'Sin función');
                    $('#ver_fecha').text(new Date(a.fecha).toLocaleDateString('es-PE'));
                    $('#ver_hora_entrada').text(a.hora_entrada ? new Date('2000-01-01 ' + a.hora_entrada).toLocaleTimeString('es-PE', {hour: '2-digit', minute: '2-digit'}) : '-');
                    $('#ver_hora_salida').text(a.hora_salida ? new Date('2000-01-01 ' + a.hora_salida).toLocaleTimeString('es-PE', {hour: '2-digit', minute: '2-digit'}) : '-');
                    
                    let estadoBadge = '';
                    if (a.estado === 'presente') estadoBadge = '<span class="badge badge-presente">Presente</span>';
                    else if (a.estado === 'ausente') estadoBadge = '<span class="badge badge-ausente">Ausente</span>';
                    else if (a.estado === 'tardanza') estadoBadge = '<span class="badge badge-tardanza">Tardanza</span>';
                    else if (a.estado === 'permiso') estadoBadge = '<span class="badge badge-permiso">Permiso</span>';
                    
                    $('#ver_estado').html(estadoBadge);
                    $('#ver_observaciones').text(a.observaciones || '-');
                    
                    $('#modalVerAsistencia').modal('show');
                }
            }
        });
    });

    $('.btnEditarAsistencia').on('click', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/asistencias/${id}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const a = response.asistencia;
                    $('#asistencia_id').val(a.id);
                    $('#personal_id').val(a.personal_id).trigger('change');
                    
                    let fecha = a.fecha;
                    if (fecha.includes('T')) {
                        fecha = fecha.split('T')[0];
                    }
                    $('#fecha').val(fecha);
                    
                    $('#hora_entrada').val(a.hora_entrada ? a.hora_entrada.substring(0, 5) : '');
                    $('#hora_salida').val(a.hora_salida ? a.hora_salida.substring(0, 5) : '');
                    $('#estado').val(a.estado);
                    $('#observaciones').val(a.observaciones);

                    toggleHoraEntradaRequirement(); 
                    
                    $('#modalAsistenciaLabel').text('Editar Asistencia');
                    $('#modalAsistencia').modal('show');
                }
            }
        });
    });

    $('.btnEliminarAsistencia').on('click', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción eliminará el registro de asistencia",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/asistencias/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Error al eliminar'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush