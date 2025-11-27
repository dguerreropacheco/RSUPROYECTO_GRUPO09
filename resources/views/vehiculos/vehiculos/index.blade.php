@extends('layouts.app')

@push('styles')
<style>
.badge-disponible {
    background-color: #28a745;
}
.badge-no-disponible {
    background-color: #dc3545;
}

.vehiculo-img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
}


#logo-preview {
    width: 60px;
    height: 60px;
    object-fit: contain;
    border-radius: 4px;
    background-color: #f8f9fa;
    padding: 4px;
    border: 1px solid #dee2e6;
}

#logo-preview {
    text-align: center;
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
                <h1 class="m-0">Vehículos</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item">Vehículos</li>
                    <li class="breadcrumb-item active">Vehículos</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Vehículos</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
                        <i class="fas fa-plus"></i> Nuevo Vehículo
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="tablaVehiculos" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Placa</th>
                            <th>Marca/Modelo</th>
                            <th>Tipo</th>
                            <th>Año</th>
                            <th>Estado</th>
                            <th>Disponible</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVehiculo" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nuevo Vehículo</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formVehiculo">
                <div class="modal-body">
                    <input type="hidden" id="vehiculo_id" name="vehiculo_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="codigo">Código <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Ingrese el código (Ej. VEH-001)" required>
                            </div>

                            <div class="form-group">
                                <label for="nombre">Nombre del vehículo <span class="text-danger">*</span></label> </label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese el nombre (Ej. VEHICULO01)" required>
                            </div>

                            <div class="form-group">
                                <label for="anio">Año <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="anio" name="anio" 
                                       min="1990" max="{{ date('Y') + 1 }}" placeholder="Ingrese el año (Ej. 2025)" required>
                            </div>

                            <div class="form-group">
                                <label for="marca_id">Marca <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="marca_id" name="marca_id" required>
                                    <option value="">Seleccione una marca</option>
                                    @foreach($marcas as $marca)
                                        <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="capacidad_carga">Capacidad de Carga (Kg) <span class="text-danger">*</span></label></label>
                                <input type="number" class="form-control" id="capacidad_carga" name="capacidad_carga" 
                                       step="0.01" min="1" placeholder="Ingrese la capacidad de carga (Ej. 9528)" required>
                            </div>

                            <div class="form-group">
                                <label for="capacidad_compactacion">Capacidad de Compactación (Kg) <span class="text-danger">*</span></label></label>
                                <input type="number" class="form-control" id="capacidad_compactacion" name="capacidad_compactacion" 
                                       step="0.01" min="1" placeholder="Ingrese la capacidad de compactación (Ej. 180)" required>
                            </div>

                        </div>
                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="tipo_vehiculo_id">Tipo de vehículo<span class="text-danger">*</span></label>
                                <select class="form-control select2" id="tipo_vehiculo_id" name="tipo_vehiculo_id" required>
                                    <option value="">Seleccione un tipo</option>
                                    @foreach($tipos as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="placa">Placa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="placa" name="placa" 
                                       placeholder="Ingrese la placa (Ej. ABC123, AB-1234 o ABC-123)" required>
                            </div>

                            <div class="form-group">
                                <label for="color_id">Color <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="color_id" name="color_id" required>
                                    <option value="">Seleccione un color</option>
                                    @foreach($colores as $color)
                                        <option value="{{ $color->id }}" data-color="{{ $color->codigo_rgb }}">
                                            {{ $color->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="modelo_id">Modelo <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="modelo_id" name="modelo_id" required disabled>
                                    <option value="">Primero seleccione una marca</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="capacidad_combustible">Capacidad de Combustible (L) <span class="text-danger">*</span></label></label>
                                <input type="number" class="form-control" id="capacidad_combustible" name="capacidad_combustible" 
                                       step="0.01" min="1" placeholder="Ingrese la capacidad de combustible (Ej. 100)" required>
                            </div>

                            <div class="form-group">
                                <label for="capacidad_ocupacion">Capacidad de Personas <span class="text-danger">*</span></label></label>
                                <input type="number" class="form-control" id="capacidad_ocupacion" name="capacidad_ocupacion" 
                                       min="1" placeholder="Ingrese la capacidad de ocupación (Ej. 2)" required>
                            </div>
                            
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="observaciones">Descripción</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="disponible" name="disponible" checked>
                                    <label class="custom-control-label" for="disponible">Disponible</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="activo" name="activo" checked>
                                    <label class="custom-control-label" for="activo">Activo</label>
                                </div>
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

<div class="modal fade" id="modalImagenes" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gestión de Imágenes</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="imagen_vehiculo_id">
                
                <div class="mb-3">
                    <h6>Subir Nueva Imagen</h6>
                    <form id="formImagen" enctype="multipart/form-data">
                        <div class="input-group">
                            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" required>
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-upload"></i> Subir
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Formatos: JPG, PNG. Tamaño máximo: 2MB</small>
                    </form>
                </div>

                <hr>

                <h6>Imágenes del Vehículo</h6>
                <div id="contenedorImagenes" class="row">
                    </div>
            </div>
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

    // Inicializar DataTable con AJAX
    const tabla = $('#tablaVehiculos').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route("vehiculos.vehiculos.index") }}',
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            {
                data: 'imagen_perfil',
                orderable: false,
                render: function(data, type, row) {
                    return `<img src="${data}" alt="Vehículo" class="vehiculo-img" id="logo-preview">`;
                }
            },
            { 
                data: 'codigo',
                render: function(data) {
                    return `<strong>${data}</strong>`;
                }
            },
            { 
                data: 'nombre',
                render: function(data) {
                    return `<strong>${data}</strong>`;
                }
            },

            { 
                data: 'placa',
                render: function(data) {
                    return `<span class="badge badge-secondary">${data}</span>`;
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    return `${row.marca} ${row.modelo}`;
                }
            },
            { data: 'tipo' },
            { data: 'anio' },
            {
                data: 'activo',
                render: function(data) {
                    const badge = data ? 'success' : 'danger';
                    const text = data ? 'Activo' : 'Inactivo';
                    return `<span class="badge badge-${badge}">${text}</span>`;
                }
            },
            {
                data: 'disponible',
                render: function(data) {
                    const badge = data ? 'success' : 'danger';
                    const text = data ? 'Disponible' : 'No disponible';
                    return `<span class="badge badge-${badge}">${text}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    const disponibleBtn = row.disponible 
                        ? `<button type="button" class="btn btn-warning btn-sm btnToggleDisponible" 
                                data-id="${row.id}" data-disponible="${row.disponible}" 
                                title="Marcar no disponible">
                                <i class="fas fa-times"></i>
                           </button>`
                        : `<button type="button" class="btn btn-success btn-sm btnToggleDisponible" 
                                data-id="${row.id}" data-disponible="${row.disponible}" 
                                title="Marcar disponible">
                                <i class="fas fa-check"></i>
                           </button>`;
                    
                    return `
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-secondary btn-sm btnVerImagenes" 
                                    data-id="${row.id}" title="Ver imágenes">
                                <i class="fas fa-images"></i>
                            </button>
                            <button type="button" class="btn btn-info btn-sm btnEditar" 
                                    data-id="${row.id}" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            ${disponibleBtn}
                            <button type="button" class="btn btn-danger btn-sm btnEliminar" 
                                    data-id="${row.id}" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
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

    // Convertir placa a mayúsculas automáticamente
    $('#placa').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });

    // Cargar modelos al seleccionar marca
    $('#marca_id').change(function() {
        const marcaId = $(this).val();
        const modeloSelect = $('#modelo_id');
        
        modeloSelect.prop('disabled', true).html('<option value="">Cargando...</option>').trigger('change');

        if (marcaId) {
            $.ajax({
                url: `/vehiculos/modelos/por-marca/${marcaId}`,
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        let options = '<option value="">Seleccione un modelo</option>';
                        response.data.forEach(modelo => {
                            options += `<option value="${modelo.id}">${modelo.nombre}</option>`;
                        });
                        modeloSelect.html(options).prop('disabled', false).trigger('change');

                        // Si estamos editando, seleccionamos el modelo correcto
                        if (window.modeloSeleccionado) {
                            modeloSelect.val(window.modeloSeleccionado).trigger('change');
                            window.modeloSeleccionado = null; // limpiar
                        }
                    } else {
                        modeloSelect.html('<option value="">No hay modelos disponibles</option>').prop('disabled', true).trigger('change');
                    }
                },
                error: function() {
                    modeloSelect.html('<option value="">Error al cargar modelos</option>').prop('disabled', true).trigger('change');
                    showErrorAlert('Error al cargar los modelos');
                }
            });
        } else {
            modeloSelect.html('<option value="">Primero seleccione una marca</option>').prop('disabled', true).trigger('change');
        }
    });

    // Abrir modal para nuevo vehículo
    $('#btnNuevo').click(function() {
        resetForm('#formVehiculo');
        $('#vehiculo_id').val('');
        $('#modalTitle').text('Nuevo Vehículo');
        $('#disponible').prop('checked', true);
        $('#activo').prop('checked', true);
        $('#marca_id').val('').trigger('change');
        $('#modelo_id').val('').prop('disabled', true).trigger('change');
        $('#tipo_vehiculo_id').val('').trigger('change');
        $('#color_id').val('').trigger('change');
        $('#modalVehiculo').modal('show');
    });

    // Inicializar Select2 cuando se abre el modal
    $('#modalVehiculo').on('shown.bs.modal', function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#modalVehiculo'),
            width: '100%'
        });
    });

    // Abrir modal para editar
    $(document).on('click', '.btnEditar', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/vehiculos/vehiculos/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const v = response.data;

                    // Guardamos temporalmente el modelo para seleccionarlo después
                    window.modeloSeleccionado = v.modelo_id;

                    $('#vehiculo_id').val(v.id);
                    $('#codigo').val(v.codigo);
                    $('#placa').val(v.placa);
                    $('#marca_id').val(v.marca_id).trigger('change');
                    
                    $('#tipo_vehiculo_id').val(v.tipo_vehiculo_id).trigger('change');
                    $('#color_id').val(v.color_id).trigger('change');
                    $('#anio').val(v.anio);
                    $('#numero_motor').val(v.numero_motor);
                    $('#nombre').val(v.nombre);
                    $('#capacidad_carga').val(v.capacidad_carga);
                    $('#capacidad_ocupacion').val(v.capacidad_ocupacion);
                    $('#capacidad_compactacion').val(v.capacidad_compactacion); // AGREGADO
                    $('#capacidad_combustible').val(v.capacidad_combustible);   // AGREGADO
                    $('#observaciones').val(v.observaciones);
                    $('#disponible').prop('checked', Boolean(v.disponible));
                    $('#activo').prop('checked', Boolean(v.activo));
                    
                    $('#modalTitle').text('Editar Vehículo');
                    $('#modalVehiculo').modal('show');
                }
            },
            error: function(xhr) {
                showErrorAlert('Error al cargar los datos del vehículo');
            }
        });
    });

    // Guardar (crear o actualizar)
    $('#formVehiculo').submit(function(e) {
        e.preventDefault();
        
        const id = $('#vehiculo_id').val();
        const url = id ? `/vehiculos/vehiculos/${id}` : '/vehiculos/vehiculos';
        const method = id ? 'PUT' : 'POST';
        
        const data = {
            codigo: $('#codigo').val(),
            placa: $('#placa').val(),
            marca_id: $('#marca_id').val(),
            modelo_id: $('#modelo_id').val(),
            tipo_vehiculo_id: $('#tipo_vehiculo_id').val(),
            color_id: $('#color_id').val(),
            anio: $('#anio').val(),
            numero_motor: $('#numero_motor').val(),
            nombre: $('#nombre').val(),
            capacidad_carga: $('#capacidad_carga').val(),
            capacidad_ocupacion: $('#capacidad_ocupacion').val(),
            capacidad_compactacion: $('#capacidad_compactacion').val(), // AGREGADO
            capacidad_combustible: $('#capacidad_combustible').val(),   // AGREGADO
            observaciones: $('#observaciones').val(),
            disponible: $('#disponible').is(':checked') ? 1 : 0,
            activo: $('#activo').is(':checked') ? 1 : 0
        };

        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function(response) {
                if (response.success) {
                    $('#modalVehiculo').modal('hide');
                    showSuccessAlert(response.message);
                    tabla.ajax.reload(null, false);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors);
                } else {
                    showErrorAlert(xhr.responseJSON.message || 'Error al guardar el vehículo');
                }
            }
        });
    });

    // Toggle disponible
    $(document).on('click', '.btnToggleDisponible', function() {
        const id = $(this).data('id');
        const disponible = $(this).data('disponible');
        const mensaje = disponible ? '¿Marcar como no disponible?' : '¿Marcar como disponible?';
        
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
                    url: `/vehiculos/vehiculos/${id}/toggle-disponible`,
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            showSuccessAlert(response.message);
                            tabla.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        showErrorAlert(xhr.responseJSON.message || 'Error al cambiar la disponibilidad');
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
                url: `/vehiculos/vehiculos/${id}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        showSuccessAlert(response.message);
                        tabla.ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 409) {
                        showErrorAlert(xhr.responseJSON.message);
                    } else {
                        showErrorAlert('Error al eliminar el vehículo');
                    }
                }
            });
        });
    });

    // ========================================
    // GESTIÓN DE IMÁGENES
    // ========================================

    // Ver imágenes
    $(document).on('click', '.btnVerImagenes', function() {
        const id = $(this).data('id');
        $('#imagen_vehiculo_id').val(id);
        cargarImagenes(id);
        $('#modalImagenes').modal('show');
    });

    // Cargar imágenes del vehículo
    function cargarImagenes(vehiculoId) {
        $.ajax({
            url: `/vehiculos/vehiculos/${vehiculoId}/imagenes`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    mostrarImagenes(response.data, vehiculoId);
                }
            },
            error: function(xhr) {
                showErrorAlert('Error al cargar las imágenes');
            }
        });
    }

    // Mostrar imágenes en el modal
    function mostrarImagenes(imagenes, vehiculoId) {
        const contenedor = $('#contenedorImagenes');
        contenedor.empty();

        if (imagenes.length === 0) {
            contenedor.html('<div class="col-12"><p class="text-muted text-center">No hay imágenes cargadas</p></div>');
            return;
        }

        imagenes.forEach(imagen => {
            const html = `
                <div class="col-md-4 mb-3" id="imagen-${imagen.id}">
                    <div class="card">
                        <img src="${imagen.url}" class="card-img-top" style="height: 150px; object-fit: cover;">
                        <div class="card-body p-2 text-center">
                            ${imagen.es_perfil ? '<span class="badge badge-primary mb-2">Imagen de Perfil</span>' : ''}
                            <div class="btn-group btn-group-sm w-100" role="group">
                                ${!imagen.es_perfil ? `
                                    <button type="button" class="btn btn-info btnSetPerfil" 
                                            data-vehiculo-id="${vehiculoId}" data-imagen-id="${imagen.id}">
                                        <i class="fas fa-star"></i>
                                    </button>
                                ` : ''}
                                <button type="button" class="btn btn-danger btnEliminarImagen" 
                                        data-vehiculo-id="${vehiculoId}" data-imagen-id="${imagen.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            contenedor.append(html);
        });
    }

    // Subir imagen
    $('#formImagen').submit(function(e) {
        e.preventDefault();
        
        const vehiculoId = $('#imagen_vehiculo_id').val();
        const formData = new FormData();
        formData.append('imagen', $('#imagen')[0].files[0]);

        $.ajax({
            url: `/vehiculos/vehiculos/${vehiculoId}/imagenes`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showSuccessAlert('Imagen subida exitosamente');
                    $('#formImagen')[0].reset();
                    cargarImagenes(vehiculoId);
                    tabla.ajax.reload(null, false);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors);
                } else {
                    showErrorAlert(xhr.responseJSON.message || 'Error al subir la imagen');
                }
            }
        });
    });

    // Establecer como imagen de perfil
    $(document).on('click', '.btnSetPerfil', function() {
        const vehiculoId = $(this).data('vehiculo-id');
        const imagenId = $(this).data('imagen-id');

        $.ajax({
            url: `/vehiculos/vehiculos/${vehiculoId}/imagenes/${imagenId}/perfil`,
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    showSuccessAlert(response.message);
                    cargarImagenes(vehiculoId);
                    tabla.ajax.reload(null, false);
                }
            },
            error: function(xhr) {
                showErrorAlert(xhr.responseJSON.message || 'Error al establecer imagen de perfil');
            }
        });
    });

    // Eliminar imagen
    $(document).on('click', '.btnEliminarImagen', function() {
        const vehiculoId = $(this).data('vehiculo-id');
        const imagenId = $(this).data('imagen-id');

        Swal.fire({
            title: '¿Está seguro?',
            text: "Esta imagen se eliminará permanentemente",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/vehiculos/vehiculos/${vehiculoId}/imagenes/${imagenId}`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            showSuccessAlert(response.message);
                            cargarImagenes(vehiculoId);
                            tabla.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        showErrorAlert(xhr.responseJSON.message || 'Error al eliminar la imagen');
                    }
                });
            }
        });
    });
});
</script>
@endpush