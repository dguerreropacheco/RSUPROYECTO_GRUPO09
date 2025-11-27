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


.color-preview {
    display: inline-block;
    width: 50px;
    height: 50px;
    object-fit: contain;
    border-radius: 4px;
    padding: 4px;
    border: 1px solid #dee2e6;
      text-align: center;
  
}

</style>
@endpush


@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Colores de Vehículos</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item">Vehículos</li>
                    <li class="breadcrumb-item active">Colores</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Colores</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
                        <i class="fas fa-plus"></i> Nuevo Color
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="tablaColores" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            {{-- <th>ID</th> --}}
                            <th>Color</th>
                            <th>Nombre</th>
                            <th>Código RGB</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($colores as $color)
                        <tr>
                            {{-- <td>{{ $color->id }}</td> --}}
                            <td>
                                <span class="color-preview" style="background-color: {{ $color->codigo_rgb }}"></span>
                            </td>
                            <td>{{ $color->nombre }}</td>
                            <td><code>{{ $color->codigo_rgb }}</code></td>
                            <td>
                                <span class="badge badge-{{ $color->activo ? 'success' : 'danger' }}">
                                    {{ $color->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info btn-sm btnEditar" data-id="{{ $color->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-{{ $color->activo ? 'warning' : 'success' }} btn-sm btnToggleActivo" 
                                            data-id="{{ $color->id }}" data-activo="{{ $color->activo }}">
                                        <i class="fas fa-{{ $color->activo ? 'ban' : 'check' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btnEliminar" data-id="{{ $color->id }}">
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
<div class="modal fade" id="modalColor" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nuevo Color</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formColor">
                <div class="modal-body">
                    <input type="hidden" id="color_id" name="color_id">
                    
                    <div class="form-group">
                        <label for="nombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="codigo_rgb">Código RGB <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="color" class="form-control" id="color_picker" style="max-width: 60px;">
                            <input type="text" class="form-control" id="codigo_rgb" name="codigo_rgb" 
                                   placeholder="#FFFFFF" pattern="^#[0-9A-Fa-f]{6}$" required>
                        </div>
                        <small class="form-text text-muted">Formato: #RRGGBB (ejemplo: #FF0000)</small>
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

    // Sincronizar color picker con input de texto
    $('#color_picker').on('input', function() {
        $('#codigo_rgb').val($(this).val().toUpperCase());
    });

    $('#codigo_rgb').on('input', function() {
        const color = $(this).val();
        if (/^#[0-9A-Fa-f]{6}$/.test(color)) {
            $('#color_picker').val(color);
        }
    });

    // Inicializar DataTable
    const tabla = $('#tablaColores').DataTable({
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
        responsive: true,
        order: [[2, 'asc']]
    });

    // Abrir modal para nuevo color
    $('#btnNuevo').click(function() {
        resetForm('#formColor');
        $('#color_id').val('');
        $('#modalTitle').text('Nuevo Color');
        $('#activo').prop('checked', true);
        $('#color_picker').val('#000000');
        $('#codigo_rgb').val('#000000');
        $('#modalColor').modal('show');
    });

    // Abrir modal para editar
    $(document).on('click', '.btnEditar', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/vehiculos/colores/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const color = response.data;
                    $('#color_id').val(color.id);
                    $('#nombre').val(color.nombre);
                    $('#codigo_rgb').val(color.codigo_rgb);
                    $('#color_picker').val(color.codigo_rgb);
                    $('#descripcion').val(color.descripcion);
                    $('#activo').prop('checked', Boolean(color.activo));
                    $('#modalTitle').text('Editar Color');
                    $('#modalColor').modal('show');
                }
            },
            error: function(xhr) {
                showErrorAlert('Error al cargar los datos del color');
            }
        });
    });

    // Guardar (crear o actualizar)
    $('#formColor').submit(function(e) {
        e.preventDefault();
        
        const id = $('#color_id').val();
        const url = id ? `/vehiculos/colores/${id}` : '/vehiculos/colores';
        const method = id ? 'PUT' : 'POST';
        
        const data = {
            nombre: $('#nombre').val(),
            codigo_rgb: $('#codigo_rgb').val().toUpperCase(),
            descripcion: $('#descripcion').val(),
            activo: $('#activo').is(':checked') ? 1 : 0
        };

        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function(response) {
                if (response.success) {
                    $('#modalColor').modal('hide');
                    showSuccessAlert(response.message);
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors);
                } else {
                    showErrorAlert(xhr.responseJSON.message || 'Error al guardar el color');
                }
            }
        });
    });

    // Toggle activo/inactivo
    $(document).on('click', '.btnToggleActivo', function() {
        const id = $(this).data('id');
        const activo = $(this).data('activo');
        const mensaje = activo ? '¿Desea desactivar este color?' : '¿Desea activar este color?';
        
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
                    url: `/vehiculos/colores/${id}/toggle-activo`,
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
                url: `/vehiculos/colores/${id}`,
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
                        showErrorAlert('Error al eliminar el color');
                    }
                }
            });
        });
    });
});
</script>
@endpush