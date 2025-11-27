@extends('layouts.app')


@push('styles')
<style>
.vehiculo-img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
}
.badge-disponible {
    background-color: #28a745;
}
.badge-no-disponible {
    background-color: #dc3545;
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

.modal-header .btn-close {
    background: none;
    border: none;
    color: #fff !important;
    opacity: 1;
    font-size: 1.4rem;
}

.modal-header .btn-close:hover {
    color: #f8f9fa !important; 
}

.modal-header .close span {
    color: #fff !important;
    font-size: 1.4rem;
}

.modal-header .close span:hover {
    color: #f8f9fa !important;
}
</style>
@endpush


@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Tipos de Vehículos</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item">Vehículos</li>
                    <li class="breadcrumb-item active">Tipos</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Tipos</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
                        <i class="fas fa-plus"></i> Nuevo Tipo
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="tablaTipos" class="table table-bordered table-striped table-hover">
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
                        @foreach($tipos as $tipo)
                        <tr>
                            <td>{{ $tipo->id }}</td>
                            <td>{{ $tipo->nombre }}</td>
                            <td>{{ $tipo->descripcion ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $tipo->activo ? 'success' : 'danger' }}">
                                    {{ $tipo->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info btn-sm btnEditar" data-id="{{ $tipo->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-{{ $tipo->activo ? 'warning' : 'success' }} btn-sm btnToggleActivo" 
                                            data-id="{{ $tipo->id }}" data-activo="{{ $tipo->activo }}">
                                        <i class="fas fa-{{ $tipo->activo ? 'ban' : 'check' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btnEliminar" data-id="{{ $tipo->id }}">
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

<!-- Modal -->
<div class="modal fade" id="modalTipo" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nuevo Tipo</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formTipo">
                <div class="modal-body">
                    <input type="hidden" id="tipo_id" name="tipo_id">
                    
                    <div class="form-group">
                        <label for="nombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="activo" name="activo" checked>
                            <label class="custom-control-label" for="activo">Activo</label>
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

    // Inicializar DataTable
    const tabla = $('#tablaTipos').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        responsive: true,
        order: [[1, 'asc']]
    });

    // Abrir modal para nuevo tipo
    $('#btnNuevo').click(function() {
        resetForm('#formTipo');
        $('#tipo_id').val('');
        $('#modalTitle').text('Nuevo Tipo');
        $('#activo').prop('checked', true);
        $('#modalTipo').modal('show');
    });

    // Abrir modal para editar
    $(document).on('click', '.btnEditar', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/vehiculos/tipos/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const tipo = response.data;
                    $('#tipo_id').val(tipo.id);
                    $('#nombre').val(tipo.nombre);
                    $('#descripcion').val(tipo.descripcion);
                    $('#activo').prop('checked', Boolean(tipo.activo));
                    $('#modalTitle').text('Editar Tipo');
                    $('#modalTipo').modal('show');
                }
            },
            error: function(xhr) {
                showErrorAlert('Error al cargar los datos del tipo');
            }
        });
    });

    // Guardar (crear o actualizar)
    $('#formTipo').submit(function(e) {
        e.preventDefault();
        
        const id = $('#tipo_id').val();
        const url = id ? `/vehiculos/tipos/${id}` : '/vehiculos/tipos';
        const method = id ? 'PUT' : 'POST';
        
        const data = {
            nombre: $('#nombre').val(),
            descripcion: $('#descripcion').val(),
            activo: $('#activo').is(':checked') ? 1 : 0
        };

        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function(response) {
                if (response.success) {
                    $('#modalTipo').modal('hide');
                    showSuccessAlert(response.message);
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors);
                } else {
                    showErrorAlert(xhr.responseJSON.message || 'Error al guardar el tipo');
                }
            }
        });
    });

    // Toggle activo/inactivo
    $(document).on('click', '.btnToggleActivo', function() {
        const id = $(this).data('id');
        const activo = $(this).data('activo');
        const mensaje = activo ? '¿Desea desactivar este tipo?' : '¿Desea activar este tipo?';
        
        Swal.fire({
            title: '¿Está seguro?',
            text: mensaje,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/vehiculos/tipos/${id}/toggle-activo`,
                    type: 'PATCH',
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
            }
        });
    });

    // Eliminar
    $(document).on('click', '.btnEliminar', function() {
        const id = $(this).data('id');
        
        confirmDelete(function() {
            $.ajax({
                url: `/vehiculos/tipos/${id}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        showSuccessAlert(response.message);
                        setTimeout(() => location.reload(), 1500);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 409) {
                        showErrorAlert(xhr.responseJSON.message);
                    } else {
                        showErrorAlert('Error al eliminar el tipo');
                    }
                }
            });
        });
    });
});
</script>
@endpush