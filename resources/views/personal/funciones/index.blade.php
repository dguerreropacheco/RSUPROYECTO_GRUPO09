@extends('layouts.app')

@push('styles')
<style>
.modal-header {
    background-color: #3f6791;
    color: #fff;
}

.modal-header .close span {
    color: #fff !important;
    font-size: 1.4rem;
}

.modal-header .close span:hover {
    color: #f8f9fa !important;
}

.badge-predefinida {
    background-color: #6c757d;
}
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Tipos de Empleado</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item">Gestión de Empleados</li>
                    <li class="breadcrumb-item active">Tipos de Empleado</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Tipos de Empleado</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
                        <i class="fas fa-plus"></i> Nuevo Tipo de Empleado
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Nota:</strong> Los tipos de empleado predefinidos (Conductor y Ayudante) no pueden eliminarse ya que son requeridos para la programación de recorridos.
                </div>
                
                <table id="tablaFunciones" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Tipo</th>
                            <th>Personal Asignado</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($funciones as $funcion)
                        <tr>
                            <td><strong>{{ $funcion->nombre }}</strong></td>
                            <td>{{ $funcion->descripcion ?? 'Sin descripción' }}</td>
                            <td>
                                @if($funcion->es_predefinida)
                                    <span class="badge badge-primary">
                                        <i class="fas fa-lock"></i> Predefinida
                                    </span>
                                @else
                                    <span class="badge badge-secondary">Personalizada</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-info">{{ $funcion->personal_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $funcion->activo ? 'success' : 'danger' }}">
                                    {{ $funcion->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning btn-sm btn-editar" 
                                            data-id="{{ $funcion->id }}" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-{{ $funcion->activo ? 'success' : 'secondary' }} btn-sm btn-toggle" 
                                            data-id="{{ $funcion->id }}" 
                                            title="{{ $funcion->activo ? 'Desactivar' : 'Activar' }}">
                                        <i class="fas fa-{{ $funcion->activo ? 'toggle-on' : 'toggle-off' }}"></i>
                                    </button>
                                    @if(!$funcion->es_predefinida && $funcion->personal_count == 0)
                                        <button type="button" class="btn btn-danger btn-sm btn-eliminar" 
                                                data-id="{{ $funcion->id }}" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-secondary btn-sm" disabled
                                                title="{{ $funcion->es_predefinida ? 'No se puede eliminar: Tipo de empleado predefinido' : 'No se puede eliminar: Tiene ' . $funcion->personal_count . ' empleado(s) asignado(s)' }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
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

<!-- Modal Tipo de Empleado -->
<div class="modal fade" id="modalFuncion" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nuevo Tipo de Empleado</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formFuncion">
                <div class="modal-body">
                    <input type="hidden" id="funcion_id" name="funcion_id">
                    
                    <div class="form-group">
                        <label for="nombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               placeholder="Ej: Supervisor, Operador, etc." required>
                        <small class="form-text text-muted">
                            Nombre del tipo de empleado o cargo del personal
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                                  placeholder="Describa las responsabilidades de este tipo de empleado..."></textarea>
                        <small class="form-text text-muted">
                            Descripción opcional de las tareas y responsabilidades
                        </small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="activo" name="activo" checked>
                            <label class="custom-control-label" for="activo">Activo</label>
                        </div>
                        <small class="form-text text-muted">
                            Los tipos de empleado inactivos no estarán disponibles para asignar a nuevo personal
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Configurar token CSRF para todas las peticiones AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Función para resetear formulario
    function resetForm(formId) {
        $(formId)[0].reset();
        $(formId).find('.is-invalid').removeClass('is-invalid');
        $(formId).find('.invalid-feedback').remove();
    }

    // Inicializar DataTable (sin AJAX, datos desde vista)
    const tabla = $('#tablaFunciones').DataTable({
        language: {
            "processing": "Procesando...",
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "emptyTable": "Ningún dato disponible en esta tabla",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "loadingRecords": "Cargando...",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sortDescending": ": Activar para ordenar la columna de manera descendente"
            },
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros"
        },
        responsive: true,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [5] } // Columna de acciones no ordenable
        ]
    });

    // Abrir modal para nueva función
    $('#btnNuevo').click(function() {
        resetForm('#formFuncion');
        $('#funcion_id').val('');
        $('#modalTitle').text('Nuevo Tipo de Empleado');
        $('#activo').prop('checked', true);
        $('#modalFuncion').modal('show');
    });

    // Abrir modal para editar
    $(document).on('click', '.btn-editar', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/personal/funciones/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const funcion = response.data;
                    $('#funcion_id').val(funcion.id);
                    $('#nombre').val(funcion.nombre);
                    $('#descripcion').val(funcion.descripcion);
                    $('#activo').prop('checked', Boolean(funcion.activo));
                    
                    $('#modalTitle').text('Editar Tipo de Empleado');
                    $('#modalFuncion').modal('show');
                }
            },
            error: function(xhr) {
                showErrorAlert('Error al cargar los datos del tipo de empleado');
            }
        });
    });

    // Guardar (crear o actualizar)
    $('#formFuncion').submit(function(e) {
        e.preventDefault();
        
        const id = $('#funcion_id').val();
        const url = id ? `/personal/funciones/${id}` : '/personal/funciones';
        const method = id ? 'PUT' : 'POST';
        
        const data = {
            nombre: $('#nombre').val().trim(),
            descripcion: $('#descripcion').val().trim() || null,
            activo: $('#activo').is(':checked') ? 1 : 0
        };

        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function(response) {
                if (response.success) {
                    $('#modalFuncion').modal('hide');
                    showSuccessAlert(response.message);
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors);
                } else {
                    showErrorAlert(xhr.responseJSON.message || 'Error al guardar el tipo de empleado');
                }
            }
        });
    });

    // Toggle activo/inactivo
    $(document).on('click', '.btn-toggle', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/personal/funciones/${id}/toggle-activo`,
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    showSuccessAlert(response.message);
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                showErrorAlert(xhr.responseJSON.message || 'Error al cambiar el estado');
            }
        });
    });

    // Eliminar
    $(document).on('click', '.btn-eliminar', function() {
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
                    url: `/personal/funciones/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            showSuccessAlert(response.message);
                            setTimeout(() => location.reload(), 1500);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 403) {
                            Swal.fire({
                                icon: 'error',
                                title: 'No permitido',
                                text: 'No se puede eliminar un tipo de empleado predefinido (Conductor/Ayudante)'
                            });
                        } else if (xhr.status === 409) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'No se puede eliminar',
                                text: xhr.responseJSON.message
                            });
                        } else {
                            showErrorAlert('Error al eliminar el tipo de empleado');
                        }
                    }
                });
            }
        });
    });

    // Habilitar tooltips de Bootstrap
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush