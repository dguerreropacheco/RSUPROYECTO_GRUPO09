@extends('layouts.app')

@section('title', 'Gestión de Motivos')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-list"></i> Gestión de Motivos
                    </h5>
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevoMotivo">
                        <i class="fas fa-plus"></i> Nuevo Motivo
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="motivosTable" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($motivos as $motivo)
                                <tr data-id="{{ $motivo->id }}">
                                    <td>{{ $motivo->id }}</td>
                                    <td>{{ $motivo->nombre }}</td>
                                    <td>{{ $motivo->descripcion ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $motivo->estado_badge }}">
                                            {{ $motivo->estado_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-info btnVerMotivo"
                                                data-id="{{ $motivo->id }}"
                                                title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning btnEditarMotivo"
                                                data-id="{{ $motivo->id }}"
                                                title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-{{ $motivo->activo ? 'secondary' : 'success' }} btnToggleActivo"
                                                data-id="{{ $motivo->id }}"
                                                title="{{ $motivo->activo ? 'Desactivar' : 'Activar' }}">
                                                <i class="fas fa-{{ $motivo->activo ? 'ban' : 'check' }}"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger btnEliminarMotivo"
                                                data-id="{{ $motivo->id }}"
                                                title="Eliminar">
                                                <i class="fas fa-trash"></i>
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
</div>

<!-- Modal Crear/Editar Motivo -->
<div class="modal fade" id="modalMotivo" tabindex="-1" role="dialog" aria-labelledby="modalMotivoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalMotivoLabel">Nuevo Motivo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formMotivo">
                <div class="modal-body">
                    <input type="hidden" id="motivo_id" name="motivo_id">
                    
                    <div class="form-group">
                        <label for="nombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                            placeholder="Ingrese el nombre del motivo" required>
                        <div class="invalid-feedback" id="error-nombre"></div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" 
                            rows="3" placeholder="Ingrese una descripción (opcional)"></textarea>
                        <div class="invalid-feedback" id="error-descripcion"></div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="activo" name="activo" checked>
                            <label class="custom-control-label" for="activo">Estado Activo</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Motivo -->
<div class="modal fade" id="modalVerMotivo" tabindex="-1" role="dialog" aria-labelledby="modalVerMotivoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerMotivoLabel">Detalles del Motivo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-4">ID:</dt>
                    <dd class="col-sm-8" id="ver_id"></dd>

                    <dt class="col-sm-4">Nombre:</dt>
                    <dd class="col-sm-8" id="ver_nombre"></dd>

                    <dt class="col-sm-4">Descripción:</dt>
                    <dd class="col-sm-8" id="ver_descripcion"></dd>

                    <dt class="col-sm-4">Estado:</dt>
                    <dd class="col-sm-8" id="ver_estado"></dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Inicializar DataTable
    const table = $('#motivosTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        responsive: true,
        order: [[1, 'asc']],
        columnDefs: [
            { targets: [4], orderable: false }
        ]
    });

    // Función para limpiar el formulario
    function limpiarFormulario() {
        $('#formMotivo')[0].reset();
        $('#motivo_id').val('');
        $('#activo').prop('checked', true);
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#modalMotivoLabel').text('Nuevo Motivo');
    }

    // Función para mostrar errores de validación
    function mostrarErrores(errors) {
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        $.each(errors, function(key, value) {
            $('#' + key).addClass('is-invalid');
            $('#error-' + key).text(value[0]);
        });
    }

    // Abrir modal para nuevo motivo
    $('#btnNuevoMotivo').click(function() {
        limpiarFormulario();
        $('#modalMotivo').modal('show');
    });

    // Guardar motivo (crear o actualizar)
    $('#formMotivo').submit(function(e) {
        e.preventDefault();
        
        const motivoId = $('#motivo_id').val();
        const url = motivoId ? `/motivos/${motivoId}` : '/motivos';
        const method = motivoId ? 'PUT' : 'POST';
        
        const formData = {
            nombre: $('#nombre').val(),
            descripcion: $('#descripcion').val(),
            activo: $('#activo').is(':checked') ? 1 : 0,
        };

        $.ajax({
            url: url,
            type: method,
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#modalMotivo').modal('hide');
                    
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
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
                    mostrarErrores(xhr.responseJSON.errors);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON.message || 'Ocurrió un error al procesar la solicitud'
                    });
                }
            }
        });
    });

    // Ver motivo
    $(document).on('click', '.btnVerMotivo', function() {
        const motivoId = $(this).data('id');
        
        $.ajax({
            url: `/motivos/${motivoId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const motivo = response.data;
                    $('#ver_id').text(motivo.id);
                    $('#ver_nombre').text(motivo.nombre);
                    $('#ver_descripcion').text(motivo.descripcion || 'N/A');
                    $('#ver_estado').html(
                        `<span class="badge badge-${motivo.activo ? 'success' : 'secondary'}">
                            ${motivo.activo ? 'Activo' : 'Inactivo'}
                        </span>`
                    );
                    $('#modalVerMotivo').modal('show');
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo obtener la información del motivo'
                });
            }
        });
    });

    // Editar motivo
    $(document).on('click', '.btnEditarMotivo', function() {
        const motivoId = $(this).data('id');
        
        $.ajax({
            url: `/motivos/${motivoId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const motivo = response.data;
                    limpiarFormulario();
                    $('#motivo_id').val(motivo.id);
                    $('#nombre').val(motivo.nombre);
                    $('#descripcion').val(motivo.descripcion);
                    $('#activo').prop('checked', motivo.activo);
                    $('#modalMotivoLabel').text('Editar Motivo');
                    $('#modalMotivo').modal('show');
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo obtener la información del motivo'
                });
            }
        });
    });

    // Toggle estado activo
    $(document).on('click', '.btnToggleActivo', function() {
        const motivoId = $(this).data('id');
        
        Swal.fire({
            title: '¿Cambiar estado?',
            text: "Se cambiará el estado del motivo",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/motivos/${motivoId}/toggle-activo`,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
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
                            text: xhr.responseJSON.message || 'No se pudo cambiar el estado'
                        });
                    }
                });
            }
        });
    });

    // Eliminar motivo
    $(document).on('click', '.btnEliminarMotivo', function() {
        const motivoId = $(this).data('id');
        
        Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/motivos/${motivoId}`,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Eliminado!',
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
                            text: xhr.responseJSON.message || 'No se pudo eliminar el motivo'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush










