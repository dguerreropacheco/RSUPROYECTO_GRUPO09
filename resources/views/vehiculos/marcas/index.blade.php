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
.marca-logo {
    width: 60px;
    height: 60px;
    object-fit: contain;
    border-radius: 4px;
    background-color: #f8f9fa;
    padding: 4px;
    border: 1px solid #dee2e6;
}
.logo-preview {
    max-width: 150px;
    max-height: 150px;
    width: auto;
    height: auto;
    object-fit: contain;
    margin-top: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    background-color: #f8f9fa;
    display: block;
}
#logo-preview-container {
    text-align: center;
}
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Marcas de Vehículos</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item">Vehículos</li>
                    <li class="breadcrumb-item active">Marcas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Marcas</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
                        <i class="fas fa-plus"></i> Nueva Marca
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="tablaMarcas" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($marcas as $marca)
                        <tr>
                            <td class="text-center">
                                <img src="{{ $marca->logo_url }}" alt="{{ $marca->nombre }}" class="marca-logo">
                            </td>
                            <td>{{ $marca->nombre }}</td>
                            <td>{{ $marca->descripcion ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $marca->activo ? 'success' : 'danger' }}">
                                    {{ $marca->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info btn-sm btnEditar" data-id="{{ $marca->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-{{ $marca->activo ? 'warning' : 'success' }} btn-sm btnToggleActivo" 
                                            data-id="{{ $marca->id }}" data-activo="{{ $marca->activo }}">
                                        <i class="fas fa-{{ $marca->activo ? 'ban' : 'check' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btnEliminar" data-id="{{ $marca->id }}">
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
<div class="modal fade" id="modalMarca" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nueva Marca</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formMarca" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="marca_id" name="marca_id">
                    <input type="hidden" id="logo_actual" name="logo_actual">
                    
                    <div class="form-group">
                        <label for="nombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="logo">Logo</label>
                        <input type="file" class="form-control-file" id="logo" name="logo" accept="image/*">
                        <small class="form-text text-muted">Formatos: JPG, PNG, SVG. Tamaño máximo: 2MB</small>
                        
                        <div id="logo-preview-container" class="mt-2" style="display: none;">
                            <img id="logo-preview" class="logo-preview">
                            <button type="button" class="btn btn-danger btn-sm mt-2" id="btnEliminarLogo">
                                <i class="fas fa-trash"></i> Eliminar logo
                            </button>
                        </div>
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
        $('#logo-preview-container').hide();
        $('#logo-preview').attr('src', '');
        $('#logo_actual').val('');
    }

    // Vista previa del logo al seleccionar archivo
    $('#logo').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#logo-preview').attr('src', e.target.result);
                $('#logo-preview-container').show();
            };
            reader.readAsDataURL(file);
        }
    });

    // Inicializar DataTable
    const tabla = $('#tablaMarcas').DataTable({
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
        order: [[1, 'asc']]
    });

    // Abrir modal para nueva marca
    $('#btnNuevo').click(function() {
        resetForm('#formMarca');
        $('#marca_id').val('');
        $('#modalTitle').text('Nueva Marca');
        $('#activo').prop('checked', true);
        $('#btnEliminarLogo').hide();
        $('#modalMarca').modal('show');
    });

    // Abrir modal para editar
    $(document).on('click', '.btnEditar', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/vehiculos/marcas/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const marca = response.data;
                    $('#marca_id').val(marca.id);
                    $('#nombre').val(marca.nombre);
                    $('#descripcion').val(marca.descripcion);
                    $('#activo').prop('checked', Boolean(marca.activo));
                    $('#logo_actual').val(marca.logo || '');
                    
                    // Mostrar logo actual si existe
                    if (marca.logo_url) {
                        $('#logo-preview').attr('src', marca.logo_url);
                        $('#logo-preview-container').show();
                        $('#btnEliminarLogo').show();
                    } else {
                        $('#logo-preview-container').hide();
                    }
                    
                    $('#modalTitle').text('Editar Marca');
                    $('#modalMarca').modal('show');
                }
            },
            error: function(xhr) {
                showErrorAlert('Error al cargar los datos de la marca');
            }
        });
    });

    // Guardar (crear o actualizar)
    $('#formMarca').submit(function(e) {
        e.preventDefault();
        
        const id = $('#marca_id').val();
        const url = id ? `/vehiculos/marcas/${id}` : '/vehiculos/marcas';
        const method = id ? 'PUT' : 'POST';
        
        const formData = new FormData();
        formData.append('nombre', $('#nombre').val());
        formData.append('descripcion', $('#descripcion').val() || '');
        formData.append('activo', $('#activo').is(':checked') ? 1 : 0);
        
        // Agregar logo si se seleccionó uno nuevo
        if ($('#logo')[0].files.length > 0) {
            formData.append('logo', $('#logo')[0].files[0]);
        }
        
        // Para PUT, agregar _method
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#modalMarca').modal('hide');
                    showSuccessAlert(response.message);
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors);
                } else {
                    showErrorAlert(xhr.responseJSON.message || 'Error al guardar la marca');
                }
            }
        });
    });

    // Eliminar logo
    $('#btnEliminarLogo').click(function() {
        const marcaId = $('#marca_id').val();
        
        if (!marcaId) {
            $('#logo-preview-container').hide();
            $('#logo').val('');
            return;
        }

        Swal.fire({
            title: '¿Está seguro?',
            text: "Se eliminará el logo de la marca",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/vehiculos/marcas/${marcaId}/delete-logo`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            showSuccessAlert(response.message);
                            $('#logo-preview-container').hide();
                            $('#logo').val('');
                            $('#logo_actual').val('');
                        }
                    },
                    error: function(xhr) {
                        showErrorAlert(xhr.responseJSON.message || 'Error al eliminar el logo');
                    }
                });
            }
        });
    });

    // Toggle activo/inactivo
    $(document).on('click', '.btnToggleActivo', function() {
        const id = $(this).data('id');
        const activo = $(this).data('activo');
        const mensaje = activo ? '¿Desea desactivar esta marca?' : '¿Desea activar esta marca?';
        
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
                    url: `/vehiculos/marcas/${id}/toggle-activo`,
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
                url: `/vehiculos/marcas/${id}`,
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
                        showErrorAlert('Error al eliminar la marca');
                    }
                }
            });
        });
    });
});
</script>
@endpush