@extends('layouts.app')

@section('title', 'Modificación Masiva de Programaciones')

@push('styles')
<style>
.info-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.info-card h4 {
    margin-bottom: 10px;
    font-weight: bold;
}

.info-card ul {
    margin: 0;
    padding-left: 20px;
}

.form-section {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 4px solid #3f6791;
}

.form-section h5 {
    color: #3f6791;
    font-weight: bold;
    margin-bottom: 15px;
}

.btn-debug {
    background-color: #ffc107;
    color: #000;
    border: none;
}

.btn-debug:hover {
    background-color: #e0a800;
    color: #000;
}

.swal2-wide {
    width: auto !important;
    max-width: 600px !important;
}
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Modificación Masiva de Programaciones</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('programaciones.index') }}">Programaciones</a></li>
                    <li class="breadcrumb-item active">Modificación Masiva</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        

        <!-- Formulario Principal -->
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">
                    <i class="fas fa-exchange-alt"></i> Configurar Cambio Masivo
                </h3>
                <div class="card-tools">
                    <a href="{{ route('programaciones.index') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-left"></i> Volver a Programaciones
                    </a>
                </div>
            </div>

            <form id="formModificacionMasiva">
                @csrf
                <div class="card-body">
                    
                    <!-- Sección 1: Rango de Fechas -->
                    <div class="form-section">
                        <h5><i class="fas fa-calendar-alt"></i> 1. Seleccione el Rango de Fechas</h5>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="masiva_fecha_inicio">Fecha de Inicio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="masiva_fecha_inicio" name="fecha_inicio" required>
                                <small class="text-muted">Se modificarán todas las programaciones desde esta fecha</small>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label for="masiva_fecha_fin">Fecha de Fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="masiva_fecha_fin" name="fecha_fin" required>
                                <small class="text-muted">Se modificarán todas las programaciones hasta esta fecha</small>
                            </div>
                        </div>
                    </div>

                    <!-- Sección 2: Filtro de Zona (Opcional) -->
                    <div class="form-section">
                        <h5><i class="fas fa-map-marker-alt"></i> 2. Filtrar por Zona (Opcional)</h5>
                        <div class="form-group">
                            <label for="masiva_zona_id">Zona</label>
                            <select class="form-control" id="masiva_zona_id" name="zona_id" style="width: 100%;">
                                <option value="">Todas las zonas</option>
                                @foreach ($zonas ?? [] as $zona)
                                    <option value="{{ $zona->id }}">{{ $zona->nombre }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Si no selecciona zona, se aplicará el cambio a todas las zonas en el rango de fechas</small>
                        </div>
                    </div>

                    <!-- Sección 3: Tipo de Cambio -->
                    <div class="form-section">
                        <h5><i class="fas fa-cogs"></i> 3. Seleccione el Tipo de Cambio</h5>
                        <div class="form-group">
                            <label for="masiva_tipo_cambio">Tipo de Cambio <span class="text-danger">*</span></label>
                            <select class="form-control" id="masiva_tipo_cambio" name="tipo_cambio" required>
                                <option value="">Seleccione tipo de cambio</option>
                                <option value="conductor">Cambio de Conductor</option>
                                <option value="ocupante">Cambio de Ocupante (Ayudante)</option>
                                <option value="turno">Cambio de Turno</option>
                                <option value="vehiculo">Cambio de Vehículo</option>
                            </select>
                        </div>

                        <!-- Campos Dinámicos según tipo de cambio -->
                        <div id="cambio_conductor_fields" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> El sistema reemplazará <strong>todos los conductores</strong> en las programaciones encontradas por el que seleccione.
                            </div>
                            <div class="form-group">
                                <label for="masiva_conductor_nuevo">Nuevo Conductor <span class="text-danger">*</span></label>
                                <select class="form-control" id="masiva_conductor_nuevo" name="conductor_nuevo">
                                    <option value="">Seleccione nuevo conductor</option>
                                    @foreach ($conductores ?? [] as $c)
                                        <option value="{{ $c->id }}">{{ $c->nombre_completo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="cambio_ocupante_fields" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> El sistema reemplazará <strong>todos los ocupantes (ayudantes)</strong> en las programaciones encontradas.
                            </div>
                            <div class="form-group">
                                <label for="masiva_ocupante_nuevo">Nuevo Ocupante <span class="text-danger">*</span></label>
                                <select class="form-control" id="masiva_ocupante_nuevo" name="ocupante_nuevo">
                                    <option value="">Seleccione nuevo ocupante</option>
                                    @foreach ($ayudantes ?? [] as $a)
                                        <option value="{{ $a->id }}">{{ $a->nombre_completo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="cambio_turno_fields" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> El sistema cambiará el turno de <strong>todas las programaciones</strong> encontradas.
                            </div>
                            <div class="form-group">
                                <label for="masiva_turno_nuevo">Nuevo Turno <span class="text-danger">*</span></label>
                                <select class="form-control" id="masiva_turno_nuevo" name="turno_nuevo">
                                    <option value="">Seleccione nuevo turno</option>
                                    @foreach ($turnos ?? [] as $turno)
                                        <option value="{{ $turno->id }}">{{ $turno->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="cambio_vehiculo_fields" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> El sistema reemplazará <strong>todos los vehículos</strong> en las programaciones encontradas.
                            </div>
                            <div class="form-group">
                                <label for="masiva_vehiculo_nuevo">Nuevo Vehículo <span class="text-danger">*</span></label>
                                <select class="form-control" id="masiva_vehiculo_nuevo" name="vehiculo_nuevo">
                                    <option value="">Seleccione nuevo vehículo</option>
                                    @foreach ($vehiculos ?? [] as $v)
                                        <option value="{{ $v->id }}">{{ $v->codigo }} - {{ $v->placa }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sección 4: Motivo y Notas -->
                    <div class="form-section">
                        <h5><i class="fas fa-clipboard-list"></i> 4. Motivo del Cambio</h5>
                        <div class="form-group">
                            <label for="masiva_motivo_id">Motivo <span class="text-danger">*</span></label>
                            <select class="form-control" id="masiva_motivo_id" name="motivo_id" required>
                                <option value="">Seleccione motivo</option>
                                @foreach ($motivos ?? [] as $motivo)
                                    <option value="{{ $motivo->id }}">{{ $motivo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="masiva_notas">Notas Adicionales (Opcional)</label>
                            <textarea class="form-control" id="masiva_notas" name="notas" rows="3" placeholder="Ingrese cualquier observación o detalle adicional sobre este cambio masivo..."></textarea>
                        </div>
                    </div>

                    <!-- Mensajes de validación -->
                    <div id="masiva-validation-messages" class="mt-3"></div>

                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            {{-- <button type="button" class="btn btn-debug" onclick="debugBusquedaMasiva()">
                                <i class="fas fa-bug"></i> Vista Previa (Debug)
                            </button> --}}
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('programaciones.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success" id="btnGuardarModificacionMasiva">
                                <i class="fas fa-save"></i> Aplicar Cambios Masivos
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Inicializar Select2
    $('#masiva_zona_id, #masiva_tipo_cambio, #masiva_conductor_nuevo, #masiva_ocupante_nuevo, #masiva_turno_nuevo, #masiva_vehiculo_nuevo, #masiva_motivo_id').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Mostrar/Ocultar campos según tipo de cambio
    $('#masiva_tipo_cambio').on('change', function() {
        const tipoCambio = $(this).val();
        
        // Ocultar todos los campos dinámicos
        $('#cambio_conductor_fields, #cambio_ocupante_fields, #cambio_turno_fields, #cambio_vehiculo_fields').hide();
        
        // Limpiar validaciones
        $('#masiva_conductor_nuevo, #masiva_ocupante_nuevo, #masiva_turno_nuevo, #masiva_vehiculo_nuevo').removeAttr('required').val('').trigger('change');
        
        // Mostrar campos según tipo de cambio
        if (tipoCambio === 'conductor') {
            $('#cambio_conductor_fields').show();
            $('#masiva_conductor_nuevo').attr('required', 'required');
        } else if (tipoCambio === 'ocupante') {
            $('#cambio_ocupante_fields').show();
            $('#masiva_ocupante_nuevo').attr('required', 'required');
        } else if (tipoCambio === 'turno') {
            $('#cambio_turno_fields').show();
            $('#masiva_turno_nuevo').attr('required', 'required');
        } else if (tipoCambio === 'vehiculo') {
            $('#cambio_vehiculo_fields').show();
            $('#masiva_vehiculo_nuevo').attr('required', 'required');
        }
    });

    // Submit del formulario
    $('#formModificacionMasiva').submit(async function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const formData = {
            _token: '{{ csrf_token() }}',
            fecha_inicio: $('#masiva_fecha_inicio').val(),
            fecha_fin: $('#masiva_fecha_fin').val(),
            zona_id: $('#masiva_zona_id').val() || '',
            tipo_cambio: $('#masiva_tipo_cambio').val(),
            motivo_id: $('#masiva_motivo_id').val(),
            notas: $('#masiva_notas').val() || '',
        };

        // Agregar campos específicos según tipo de cambio
        const tipoCambio = formData.tipo_cambio;
        if (tipoCambio === 'conductor') {
            formData.conductor_nuevo = $('#masiva_conductor_nuevo').val();
        } else if (tipoCambio === 'ocupante') {
            formData.ocupante_nuevo = $('#masiva_ocupante_nuevo').val();
        } else if (tipoCambio === 'turno') {
            formData.turno_nuevo = $('#masiva_turno_nuevo').val();
        } else if (tipoCambio === 'vehiculo') {
            formData.vehiculo_nuevo = $('#masiva_vehiculo_nuevo').val();
        }

        // Validación básica
        if (!formData.fecha_inicio || !formData.fecha_fin || !formData.tipo_cambio || !formData.motivo_id) {
            Swal.fire('Validación', 'Complete todos los campos requeridos.', 'warning');
            return;
        }

        if (new Date(formData.fecha_fin) < new Date(formData.fecha_inicio)) {
            Swal.fire('Validación', 'La fecha de fin debe ser mayor o igual a la fecha de inicio.', 'warning');
            return;
        }

        // Validar que se haya seleccionado un nuevo valor
        let nuevoValor = null;
        if (tipoCambio === 'conductor') {
            nuevoValor = formData.conductor_nuevo;
        } else if (tipoCambio === 'ocupante') {
            nuevoValor = formData.ocupante_nuevo;
        } else if (tipoCambio === 'turno') {
            nuevoValor = formData.turno_nuevo;
        } else if (tipoCambio === 'vehiculo') {
            nuevoValor = formData.vehiculo_nuevo;
        }

        if (!nuevoValor) {
            Swal.fire('Validación', 'Debe seleccionar el nuevo valor para el tipo de cambio.', 'warning');
            return;
        }

        // Mostrar loading
        Swal.fire({
            title: 'Procesando...',
            icon: 'info',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const response = await $.ajax({
                url: '/programacion/modificacion-masiva',
                type: 'POST',
                data: formData,
            });

            if (response.success) {
                let htmlContent = `<div class="alert alert-success">${response.message}</div>`;
                
                if (response.datos) {
                    htmlContent += '<div class="alert alert-info mt-3">';
                    htmlContent += '<strong>📊 Resumen de operación:</strong>';
                    htmlContent += '<ul class="mt-2" style="padding-left: 20px;">';
                    htmlContent += `<li>Programaciones encontradas: <strong>${response.datos.programaciones_encontradas}</strong></li>`;
                    htmlContent += `<li>Programaciones actualizadas: <strong>${response.datos.programaciones_actualizadas}</strong></li>`;
                    htmlContent += `<li>Cambios registrados: <strong>${response.datos.cambios_registrados}</strong></li>`;
                    if (response.datos.programaciones_saltadas > 0) {
                        htmlContent += `<li>Programaciones sin cambios: <strong>${response.datos.programaciones_saltadas}</strong></li>`;
                    }
                    htmlContent += '</ul>';
                    htmlContent += '</div>';
                }
                
                Swal.fire({
                    title: '✅ Éxito',
                    html: htmlContent,
                    icon: 'success',
                    allowOutsideClick: true
                }).then(() => {
                    window.location.href = '{{ route("programaciones.index") }}';
                });
            } else {
                mostrarErrorDetallado(response);
            }
        } catch (xhr) {
            manejarErrorAjax(xhr);
        }
    });
});

// Función de debug
window.debugBusquedaMasiva = async function() {
    const fechaInicio = $('#masiva_fecha_inicio').val();
    const fechaFin = $('#masiva_fecha_fin').val();
    const zonaId = $('#masiva_zona_id').val() || '';

    if (!fechaInicio || !fechaFin) {
        Swal.fire('Atención', 'Por favor ingrese ambas fechas', 'warning');
        return;
    }

    try {
        const response = await $.ajax({
            url: '/programacion/debug-busqueda',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin,
                zona_id: zonaId || null
            }
        });

        let mensaje = `📊 VISTA PREVIA DE BÚSQUEDA:\n\n`;
        mensaje += `📅 Rango: ${response.debug.fecha_inicio} a ${response.debug.fecha_fin}\n`;
        mensaje += `📍 Zona: ${response.debug.zona_id || 'Todas las zonas'}\n\n`;
        mensaje += `RESULTADOS:\n`;
        mensaje += `• Total programaciones encontradas: ${response.total_todas}\n`;
        mensaje += `• Programaciones activas: ${response.total_activas}\n`;
        if (response.total_con_zona !== null) {
            mensaje += `• Con filtro de zona: ${response.total_con_zona}\n`;
        }

        Swal.fire({
            title: 'Vista Previa',
            text: mensaje,
            icon: 'info',
            confirmButtonText: 'Entendido'
        });
        
        console.log('Debug completo:', response);
    } catch(err) {
        console.error('Error en debug:', err);
        Swal.fire('Error', 'Error al realizar la vista previa', 'error');
    }
};

function mostrarErrorDetallado(response) {
    let htmlContent = `<div class="alert alert-danger">${response.message}</div>`;
    
    if (response.tipo === 'conflictos_personal' && response.conflictos && response.conflictos.length > 0) {
        htmlContent += '<div class="alert alert-warning mt-3">';
        htmlContent += '<h6><i class="fas fa-exclamation-triangle"></i> Conflictos detectados:</h6>';
        htmlContent += '<div style="max-height: 300px; overflow-y: auto;">';
        htmlContent += '<ul>';
        
        response.conflictos.forEach(conflicto => {
            htmlContent += `<li>`;
            htmlContent += `<strong>📌 Programación #${conflicto.programacion_id}</strong><br>`;
            htmlContent += `📅 Fecha: ${conflicto.fecha}<br>`;
            htmlContent += `<span style="color: #d32f2f;"><strong>❌ Conflicto:</strong> ${conflicto.motivo}</span>`;
            htmlContent += `</li>`;
        });
        
        htmlContent += '</ul>';
        htmlContent += '</div>';
        htmlContent += `<p class="mt-2"><strong>Total:</strong> ${response.conflictos.length} conflicto(s)</p>`;
        htmlContent += '</div>';
    }
    
    Swal.fire({
        title: '❌ No se pudo completar',
        html: htmlContent,
        icon: 'error',
        customClass: { popup: 'swal2-wide' }
    });
}

function manejarErrorAjax(xhr) {
    let errorMsg = 'Error al procesar la modificación masiva.';
    let htmlContent = '';
    
    if (xhr.status === 422 && xhr.responseJSON) {
        const responseData = xhr.responseJSON;
        errorMsg = responseData.message || 'Errores de validación.';
        
        if (responseData.tipo === 'conflictos_personal' && responseData.conflictos) {
            htmlContent = `<div class="alert alert-danger">${errorMsg}</div>`;
            htmlContent += '<div class="alert alert-warning mt-3">';
            htmlContent += '<h6><i class="fas fa-exclamation-triangle"></i> Conflictos:</h6>';
            htmlContent += '<ul>';
            
            responseData.conflictos.forEach(c => {
                htmlContent += `<li><strong>#${c.programacion_id}</strong> - ${c.fecha}: ${c.motivo}</li>`;
            });
            
            htmlContent += '</ul>';
            htmlContent += '</div>';
        } else {
            htmlContent = `<div class="alert alert-danger">${errorMsg}</div>`;
        }
    } else if (xhr.status === 404 && xhr.responseJSON) {
        errorMsg = xhr.responseJSON.message || 'No se encontraron programaciones.';
        htmlContent = `<div class="alert alert-danger">${errorMsg}</div>`;
    } else {
        htmlContent = `<div class="alert alert-danger">${errorMsg}</div>`;
    }

    Swal.fire({
        title: '❌ Error',
        html: htmlContent,
        icon: 'error',
        customClass: { popup: 'swal2-wide' }
    });
}
</script>
@endpush