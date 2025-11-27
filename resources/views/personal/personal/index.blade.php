@extends('layouts.app')

@push('styles')
<style>
.personal-foto {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #dee2e6;
}

.foto-preview {
    max-width: 200px;
    max-height: 200px;
    width: auto;
    height: auto;
    object-fit: cover;
    margin-top: 10px;
    border: 2px solid #ddd;
    border-radius: 8px;
    padding: 5px;
    background-color: #f8f9fa;
    display: block;
}

#foto-preview-container {
    text-align: center;
}

.badge-activo {
    background-color: #28a745;
}

.badge-inactivo {
    background-color: #dc3545;
}

.badge-contratado {
    background-color: #17a2b8;
}

.badge-sin-contrato {
    background-color: #6c757d;
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

.licencia-info {
    background-color: #fff3cd;
    border: 1px solid #ffc107;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 15px;
    display: none;
}

.licencia-info.show {
    display: block;
}

.required-field::after {
    content: " *";
    color: red;
}

.info-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 0.25rem;
}
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Empleados</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item">Gestión de Empleados</li>
                    <li class="breadcrumb-item active">Empleados</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Empleados</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
                        <i class="fas fa-plus"></i> Nuevo Empleado
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="tablaPersonal" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>DNI</th>
                            <th>Nombre Completo</th>
                            <th>Tipo de Empleado</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Contrato</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($personal as $persona)
                        <tr>
                            <td class="text-center">
                                <img src="{{ $persona->foto_url }}" alt="{{ $persona->nombre_completo }}" class="personal-foto">
                            </td>
                            <td>{{ $persona->dni }}</td>
                            <td>{{ $persona->nombre_completo }}</td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $persona->funcion->nombre ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $persona->telefono ?? 'N/A' }}</td>
                            <td>{{ $persona->email ?? 'N/A' }}</td>
                            <td>
                                @if($persona->contratoActivo)
                                    <span class="badge badge-contratado">
                                        <i class="fas fa-check-circle"></i> Contratado
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        {{ ucfirst($persona->contratoActivo->tipo_contrato) }}
                                    </small>
                                @else
                                    <span class="badge badge-sin-contrato">
                                        <i class="fas fa-times-circle"></i> Sin Contrato
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $persona->activo ? 'activo' : 'inactivo' }}">
                                    {{ $persona->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info btn-sm btnEditar" 
                                            data-id="{{ $persona->id }}" 
                                            title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-{{ $persona->activo ? 'warning' : 'success' }} btn-sm btnToggleActivo" 
                                            data-id="{{ $persona->id }}" 
                                            data-activo="{{ $persona->activo }}"
                                            title="{{ $persona->activo ? 'Desactivar' : 'Activar' }}">
                                        <i class="fas fa-{{ $persona->activo ? 'ban' : 'check' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btnEliminar" 
                                            data-id="{{ $persona->id }}"
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

<!-- Modal Empleado -->
<div class="modal fade" id="modalPersonal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nuevo Empleado</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formPersonal" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="personal_id" name="personal_id">
                    <input type="hidden" id="foto_actual" name="foto_actual">

                    <!-- Información Personal -->
                    <h6 class="text-primary mb-3"><i class="fas fa-user"></i> Información Personal</h6>
                    
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="dni" class="required-field">DNI</label>
                            <input type="text" class="form-control" id="dni" name="dni" 
                                   maxlength="8" pattern="[0-9]{8}" 
                                   placeholder="12345678" required>
                            <small class="form-text text-muted">8 dígitos numéricos</small>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="nombres" class="required-field">Nombres</label>
                            <input type="text" class="form-control" id="nombres" name="nombres" 
                                   placeholder="Juan Carlos" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="fecha_nacimiento" class="required-field">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" 
                                   name="fecha_nacimiento" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="apellido_paterno" class="required-field">Apellido Paterno</label>
                            <input type="text" class="form-control" id="apellido_paterno" 
                                   name="apellido_paterno" placeholder="Pérez" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="apellido_materno" class="required-field">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellido_materno" 
                                   name="apellido_materno" placeholder="García" required>
                        </div>
                    </div>

                    <!-- Información de Contacto -->
                    <h6 class="text-primary mb-3 mt-3"><i class="fas fa-phone"></i> Información de Contacto</h6>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                   placeholder="987654321">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="ejemplo@correo.com">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <textarea class="form-control" id="direccion" name="direccion" 
                                  rows="2" placeholder="Av. Principal 123, Distrito, Ciudad"></textarea>
                    </div>

                    <!-- Información Laboral -->
                    <h6 class="text-primary mb-3 mt-3"><i class="fas fa-briefcase"></i> Información Laboral</h6>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="funcion_id" class="required-field">Tipo de Empleado</label>
                            <select class="form-control select2" id="funcion_id" name="funcion_id" required>
                                <option value="">Seleccione un tipo de empleado</option>
                                @foreach($funciones as $funcion)
                                    <option value="{{ $funcion->id }}" data-nombre="{{ $funcion->nombre }}">
                                        {{ $funcion->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="clave" class="required-field" id="label_clave">Clave de Asistencia</label>
                            <input type="password" class="form-control" id="clave" name="clave" 
                                   minlength="4" maxlength="6" placeholder="****" required>
                            <small class="form-text text-muted">4-6 caracteres para marcar asistencia</small>
                        </div>
                    </div>

                    <!-- Información de Licencia (solo para conductores) -->
                    <div class="licencia-info" id="licenciaInfo">
                        <strong><i class="fas fa-exclamation-triangle"></i> Información Importante:</strong>
                        Los conductores requieren licencia de conducir vigente.
                    </div>

                    <div class="form-row" id="licenciaFields" style="display: none;">
                        <div class="form-group col-md-6">
                            <label for="licencia_conducir" id="label_licencia">Licencia de Conducir</label>
                            <input type="text" class="form-control" id="licencia_conducir" 
                                   name="licencia_conducir" placeholder="Q12345678">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="fecha_vencimiento_licencia" id="label_vencimiento">Fecha de Vencimiento</label>
                            <input type="date" class="form-control" id="fecha_vencimiento_licencia" 
                                   name="fecha_vencimiento_licencia">
                        </div>
                    </div>

                    <!-- Foto -->
                    <h6 class="text-primary mb-3 mt-3"><i class="fas fa-camera"></i> Fotografía</h6>
                    
                    <div class="form-group">
                        <label for="foto">Foto del Empleado</label>
                        <input type="file" class="form-control-file" id="foto" name="foto" accept="image/*">
                        <small class="form-text text-muted">Formatos: JPG, PNG. Tamaño máximo: 2MB</small>
                        
                        <div id="foto-preview-container" class="mt-2" style="display: none;">
                            <img id="foto-preview" class="foto-preview">
                            <button type="button" class="btn btn-danger btn-sm mt-2" id="btnEliminarFoto">
                                <i class="fas fa-trash"></i> Eliminar foto
                            </button>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="form-group mt-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="activo" name="activo" checked>
                            <label class="custom-control-label" for="activo">Empleado Activo</label>
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Configurar token CSRF para AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Inicializar Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        dropdownParent: $('#modalPersonal')
    });

    // Inicializar DataTable
    const table = $('#tablaPersonal').DataTable({
        language: {
            "decimal": "",
            "emptyTable": "No hay datos disponibles en la tabla",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ registros",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron registros coincidentes",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        responsive: true,
        order: [[2, 'asc']], // Ordenar por nombre
        columnDefs: [
            { orderable: false, targets: [0, 8] } // Foto y Acciones no ordenables
        ]
    });

    // Preview de foto al seleccionar
    $('#foto').change(function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validar tamaño
            if (file.size > 2048000) { // 2MB
                showErrorAlert('La foto no debe superar los 2MB');
                $(this).val('');
                return;
            }

            // Validar tipo
            if (!file.type.match('image.*')) {
                showErrorAlert('El archivo debe ser una imagen');
                $(this).val('');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                $('#foto-preview').attr('src', e.target.result);
                $('#foto-preview-container').show();
            };
            reader.readAsDataURL(file);
        }
    });

    // Mostrar/ocultar campos de licencia según función
    $('#funcion_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const nombreFuncion = selectedOption.data('nombre');
        
        if (nombreFuncion === 'Conductor') {
            $('#licenciaInfo').addClass('show');
            $('#licenciaFields').show();
            $('#licencia_conducir').attr('required', true);
            $('#fecha_vencimiento_licencia').attr('required', true);
            $('#label_licencia').html('Licencia de Conducir <span class="text-danger">*</span>');
            $('#label_vencimiento').html('Fecha de Vencimiento <span class="text-danger">*</span>');
        } else {
            $('#licenciaInfo').removeClass('show');
            $('#licenciaFields').hide();
            $('#licencia_conducir').attr('required', false).val('');
            $('#fecha_vencimiento_licencia').attr('required', false).val('');
            $('#label_licencia').html('Licencia de Conducir');
            $('#label_vencimiento').html('Fecha de Vencimiento');
        }
    });

    // Validar solo números en DNI
    $('#dni').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Abrir modal para nuevo personal
    $('#btnNuevo').click(function() {
        resetForm('#formPersonal');
        $('#personal_id').val('');
        $('#modalTitle').text('Nuevo Empleado');
        $('#activo').prop('checked', true);
        $('#foto-preview-container').hide();
        $('#licenciaFields').hide();
        $('#licenciaInfo').removeClass('show');
        $('#btnEliminarFoto').hide();
        $('#label_clave').html('Clave de Asistencia <span class="text-danger">*</span>');
        $('#clave').attr('required', true).attr('placeholder', '****');
        $('.select2').val(null).trigger('change');
        $('#modalPersonal').modal('show');
    });

    // Abrir modal para editar
    $(document).on('click', '.btnEditar', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/personal/personal/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const persona = response.data;
                    
                    $('#personal_id').val(persona.id);
                    $('#dni').val(persona.dni);
                    $('#nombres').val(persona.nombres);
                    $('#apellido_paterno').val(persona.apellido_paterno);
                    $('#apellido_materno').val(persona.apellido_materno);
                    $('#fecha_nacimiento').val(persona.fecha_nacimiento);
                    $('#telefono').val(persona.telefono);
                    $('#email').val(persona.email);
                    $('#direccion').val(persona.direccion);
                    $('#licencia_conducir').val(persona.licencia_conducir);
                    $('#fecha_vencimiento_licencia').val(persona.fecha_vencimiento_licencia);
                    $('#funcion_id').val(persona.funcion_id).trigger('change');
                    $('#activo').prop('checked', Boolean(persona.activo));
                    $('#foto_actual').val(persona.foto || '');
                    
                    // Configurar clave como opcional en edición
                    $('#label_clave').text('Clave de Asistencia (dejar vacío para no cambiar)');
                    $('#clave').attr('required', false).attr('placeholder', 'Dejar vacío para no cambiar');
                    
                    // Mostrar foto actual si existe
                    if (persona.foto_url) {
                        $('#foto-preview').attr('src', persona.foto_url);
                        $('#foto-preview-container').show();
                        $('#btnEliminarFoto').show();
                    } else {
                        $('#foto-preview-container').hide();
                    }
                    
                    $('#modalTitle').text('Editar Empleado');
                    $('#modalPersonal').modal('show');
                }
            },
            error: function(xhr) {
                showErrorAlert('Error al cargar los datos del empleado');
            }
        });
    });

    // Guardar (crear o actualizar)
    $('#formPersonal').submit(function(e) {
        e.preventDefault();
        
        // Validar fecha de nacimiento (mayor de 18 años)
        const fechaNac = new Date($('#fecha_nacimiento').val());
        const hoy = new Date();
        const edad = hoy.getFullYear() - fechaNac.getFullYear();
        
        if (edad < 18) {
            showErrorAlert('El empleado debe ser mayor de 18 años');
            return;
        }
        
        const id = $('#personal_id').val();
        const url = id ? `/personal/personal/${id}` : '/personal/personal';
        const method = id ? 'PUT' : 'POST';
        
        const formData = new FormData();
        formData.append('dni', $('#dni').val());
        formData.append('nombres', $('#nombres').val());
        formData.append('apellido_paterno', $('#apellido_paterno').val());
        formData.append('apellido_materno', $('#apellido_materno').val());
        formData.append('fecha_nacimiento', $('#fecha_nacimiento').val());
        formData.append('telefono', $('#telefono').val() || '');
        formData.append('email', $('#email').val() || '');
        formData.append('direccion', $('#direccion').val() || '');
        formData.append('licencia_conducir', $('#licencia_conducir').val() || '');
        formData.append('fecha_vencimiento_licencia', $('#fecha_vencimiento_licencia').val() || '');
        formData.append('funcion_id', $('#funcion_id').val());
        formData.append('activo', $('#activo').is(':checked') ? 1 : 0);
        
        // Agregar clave solo si se ingresó
        if ($('#clave').val()) {
            formData.append('clave', $('#clave').val());
        }
        
        // Agregar foto si se seleccionó una nueva
        if ($('#foto')[0].files.length > 0) {
            formData.append('foto', $('#foto')[0].files[0]);
        }
        
        // Para PUT, agregar _method
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }

        // Mostrar loading
        Swal.fire({
            title: 'Guardando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
    if (response.success) {
        $('#modalPersonal').modal('hide');
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: response.message,
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            window.location.href = "{{ route('personal.personal.index') }}"; // Cambiar location.reload()
        });
    }
},
            error: function(xhr) {
                Swal.close();
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = '<ul class="text-left">';
                    $.each(errors, function(key, value) {
                        errorMessage += '<li>' + value[0] + '</li>';
                    });
                    errorMessage += '</ul>';
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Errores de Validación',
                        html: errorMessage
                    });
                } else {
                    showErrorAlert(xhr.responseJSON.message || 'Error al guardar el empleado');
                }
            }
        });
    });

    // Eliminar foto
    $('#btnEliminarFoto').click(function() {
        const personalId = $('#personal_id').val();
        
        if (!personalId) {
            $('#foto-preview-container').hide();
            $('#foto').val('');
            return;
        }

        Swal.fire({
            title: '¿Está seguro?',
            text: "Se eliminará la foto del personal",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/personal/personal/${personalId}/delete-foto`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            showSuccessAlert(response.message);
                            $('#foto-preview-container').hide();
                            $('#foto').val('');
                            $('#foto_actual').val('');
                        }
                    },
                    error: function(xhr) {
                        showErrorAlert(xhr.responseJSON.message || 'Error al eliminar la foto');
                    }
                });
            }
        });
    });

    // Toggle activo/inactivo
    $(document).on('click', '.btnToggleActivo', function() {
        const id = $(this).data('id');
        const activo = $(this).data('activo');
        const mensaje = activo ? '¿Desea desactivar a este personal?' : '¿Desea activar a este personal?';
        
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
                    url: `/personal/personal/${id}/toggle-activo`,
                    type: 'PATCH',
                    success: function(response) {
    if (response.success) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: response.message,
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            window.location.href = "{{ route('personal.personal.index') }}"; // Cambiar location.reload()
        });
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
    const button = $(this); // Guardar referencia al botón
    const row = button.closest('tr'); // Guardar referencia a la fila
    
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción no se puede revertir",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/personal/personal/${id}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        // Eliminar la fila de la tabla sin recargar
                        table.row(row).remove().draw(false);
                        
                        Swal.fire({
                            icon: 'success',
                            title: '¡Eliminado!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 409) {
                        Swal.fire({
                            icon: 'error',
                            title: 'No se puede eliminar',
                            text: xhr.responseJSON.message
                        });
                    } else {
                        showErrorAlert('Error al eliminar el empleado');
                    }
                }
            });
        }
    });
});

    // Función para resetear formulario
    function resetForm(formId) {
        $(formId)[0].reset();
        $('.select2').val(null).trigger('change');
        $('#foto-preview-container').hide();
        $('#licenciaFields').hide();
        $('#licenciaInfo').removeClass('show');
        
        // Limpiar errores de validación
        $(formId).find('.is-invalid').removeClass('is-invalid');
        $(formId).find('.invalid-feedback').remove();
    }

    // Función para mostrar alerta de éxito
    function showSuccessAlert(message) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: message,
            timer: 2000,
            showConfirmButton: false
        });
    }

    // Función para mostrar alerta de error
    function showErrorAlert(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    }
});
</script>
@endpush