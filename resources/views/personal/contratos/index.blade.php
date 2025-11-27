@extends('layouts.app')

@section('title', 'Contratos')

@push('styles')
<style>
.badge-permanente {
    background-color: #1dbed0ff;
}

.badge-nombrado {
    background-color: #007bff;
    color: #000;
}

.badge-temporal {
    background-color: #ffc107;
    color: #000;
}
.badge-vigente {
    background-color: #6ab27bff;
}
.badge-vencido {
    background-color: #e6516086;
}
.badge-por-vencer {
    background-color: #fc933dff;
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

.modal-header .close {
    color: #fff !important;
    opacity: 1;
}

.modal-header .close:hover {
    color: #f8f9fa !important;
}

.info-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    border-radius: 0.25rem;
}

.dias-restantes-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.dias-restantes-warning {
    background-color: #fff3cd;
    color: #856404;
}

.dias-restantes-success {
    background-color: #d4edda;
    color: #155724;
}

/* Ocultar campos según tipo de contrato */
.campo-temporal {
    display: none;
}

/* Estilos para el campo de fecha fin deshabilitado */
#fecha_fin:disabled {
    background-color: #e9ecef;
    cursor: not-allowed;
}
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Contratos</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item">Gestión de Empleados</li>
                    <li class="breadcrumb-item active">Contratos</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Tarjetas de Estadísticas -->
        <div class="row mb-3">
            {{-- <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="total-contratos">{{ $contratos->count() }}</h3>
                        <p>Total Contratos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                </div>
            </div> --}}
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="contratos-vigentes">{{ $contratos->where('activo', true)->count() }}</h3>
                        <p>Contratos Vigentes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3 id="contratos-permanentes">{{ $contratos->where('tipo_contrato', 'nombrado')->count() }}</h3>
                        <p>Nombrados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-infinity"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="contratos-temporales">{{ $contratos->where('tipo_contrato', 'permanente')->count() }}</h3>
                        <p>Permanentes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>

             <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="contratos-temporales">{{ $contratos->where('tipo_contrato', 'temporal')->count() }}</h3>
                        <p>Temporales</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Contratos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Contratos</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
                        <i class="fas fa-plus"></i> Nuevo Contrato
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="tablaContratos" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Personal</th>
                            <th>DNI</th>
                            <th>Tipo</th>
                            <th>Departamento</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Salario</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contratos as $contrato)
                        <tr>
                            <td>
                                <strong>{{ $contrato->personal->nombre_completo }}</strong><br>
                                <small class="text-muted">{{ $contrato->personal->funcion ? $contrato->personal->funcion->nombre : 'Sin función' }}</small>
                            </td>
                            <td>{{ $contrato->personal->dni }}</td>
                            <td>
                                <span class="badge badge-{{
                                                $contrato->tipo_contrato === 'permanente' ? 'permanente' : (
                                                    $contrato->tipo_contrato === 'temporal' ? 'temporal' : (
                                                        $contrato->tipo_contrato === 'nombrado' ? 'nombrado' : ''
                                                    )
                                                )
                                            }}">
                                                {{ ucfirst($contrato->tipo_contrato) }}</span>
                            </td>
                            <td>{{ $contrato->departamento ? $contrato->departamento->nombre : 'Sin departamento' }}</td>
                            <td>{{ $contrato->fecha_inicio->format('d/m/Y') }}</td>
                            <td>
                                @if($contrato->fecha_fin)
                                    {{ $contrato->fecha_fin->format('d/m/Y') }}
                                    @if($contrato->dias_restantes !== null)
                                        <br>
                                        <small class="info-badge 
                                            @if($contrato->dias_restantes < 0) dias-restantes-danger
                                            @elseif($contrato->dias_restantes <= 30) dias-restantes-warning
                                            @else dias-restantes-success
                                            @endif">
                                            @if($contrato->dias_restantes < 0)
                                                Vencido hace {{ abs($contrato->dias_restantes) }} días
                                            @else
                                                {{ $contrato->dias_restantes }} días restantes
                                            @endif
                                        </small>
                                    @endif
                                @else
                                    <span class="text-muted">Indefinido</span>
                                @endif
                            </td>
                            <td>{{ $contrato->salario_formateado }}</td>
                            <td>
                                @if($contrato->esta_vigente)
                                    <span class="badge badge-vigente">Vigente</span>
                                @else
                                    <span class="badge badge-vencido">No vigente</span>
                                @endif
                                <br>
                                <span class="badge badge-{{ $contrato->activo ? 'success' : 'secondary' }} mt-1">
                                    {{ $contrato->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info btn-sm btnEditar" 
                                            data-id="{{ $contrato->id }}" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-{{ $contrato->activo ? 'warning' : 'success' }} btn-sm btnToggleActivo" 
                                            data-id="{{ $contrato->id }}" 
                                            data-activo="{{ $contrato->activo }}"
                                            title="{{ $contrato->activo ? 'Desactivar' : 'Activar' }}">
                                        <i class="fas fa-{{ $contrato->activo ? 'ban' : 'check' }}"></i>
                                    </button>
                                    @if($contrato->esta_vigente && ($contrato->tipo_contrato === 'temporal' || $contrato->tipo_contrato === 'permanente'))
                                    <button type="button" class="btn btn-dark btn-sm btnTerminar" 
                                            data-id="{{ $contrato->id }}" title="Terminar Contrato">
                                        <i class="fas fa-hand-paper"></i>
                                    </button>
                                    @endif
                                    <button type="button" class="btn btn-danger btn-sm btnEliminar" 
                                            data-id="{{ $contrato->id }}" title="Eliminar">
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

<!-- Modal Nuevo/Editar Contrato -->
<div class="modal fade" id="modalContrato" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nuevo Contrato</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formContrato">
                <div class="modal-body">
                    <input type="hidden" id="contrato_id" name="contrato_id">
                    
                    <div class="row">
                        <!-- Personal -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="personal_id">Empleado <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="personal_id" name="personal_id" required style="width: 100%;">
                                    <option value="">Seleccione un empleado</option>
                                    @foreach($personalDisponible as $personal)
                                    <option value="{{ $personal->id }}" 
                                            data-dni="{{ $personal->dni }}"
                                            data-funcion="{{ $personal->funcion ? $personal->funcion->nombre : 'Sin función' }}">
                                        {{ $personal->nombre_completo }} - {{ $personal->dni }}
                                    </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Solo se muestran empleados sin contrato activo</small>
                            </div>
                        </div>

                        <!-- Tipo de Contrato -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_contrato">Tipo de Contrato <span class="text-danger">*</span></label>
                                <select class="form-control" id="tipo_contrato" name="tipo_contrato" required>
                                    <option value="">Seleccione un tipo</option>
                                    <option value="permanente">Permanente</option>
                                    <option value="temporal">Temporal</option>
                                     <option value="nombrado">Nombrado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Fecha Inicio -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_inicio">Fecha de Inicio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                            </div>
                        </div>

                        <!-- Fecha Fin -->
                        <div class="col-md-6">
                            <div class="form-group campo-temporal">
                                <label for="fecha_fin">Fecha de Finalización <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" onkeydown="return false">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Salario -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="salario">Salario (S/) <span class="text-danger">*</span> </label>
                                <input type="number" step="0.01" class="form-control" id="salario" name="salario" 
                                       placeholder="Ej: 1500.00" min="0">
                            </div>
                        </div>

                        <!-- Departamento -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="departamento_id">Departamento <span class="text-danger">*</span> </label>
                                <select class="form-control select2" id="departamento_id" name="departamento_id" style="width: 100%;">
                                    <option value="">Seleccione un departamento</option>
                                    @foreach($departamentos as $depto)
                                    <option value="{{ $depto->id }}">{{ $depto->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Período de Prueba -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="periodo_prueba">Período de Prueba (meses) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="periodo_prueba" name="periodo_prueba" 
                                       placeholder="Ej: 3" min="0" max="12">
                                <small class="form-text text-muted">Máximo 12 meses</small>
                            </div>
                        </div>

                        <!-- Estado Activo -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="activo" name="activo" checked>
                                    <label class="custom-control-label" for="activo">Contrato Activo</label>
                                </div>
                                <small class="form-text text-muted">Solo puede haber un contrato activo por empleado</small>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="form-group">
                        <label for="observaciones">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" 
                                  placeholder="Observaciones adicionales sobre el contrato..."></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Contrato</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Terminar Contrato -->
<div class="modal fade" id="modalTerminar" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">Terminar Contrato Anticipadamente</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formTerminar">
                <div class="modal-body">
                    <input type="hidden" id="contrato_terminar_id" name="contrato_terminar_id">
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Advertencia:</strong> Esta acción marcará el contrato como terminado y lo desactivará.
                    </div>

                    <div class="form-group">
                        <label for="fecha_terminacion">Fecha de Terminación <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="fecha_terminacion" name="fecha_terminacion" required>
                        <small class="form-text text-muted">No puede ser anterior a la fecha de inicio del contrato</small>
                    </div>

                    <div class="form-group">
                        <label for="motivo_terminacion">Motivo de Terminación <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="motivo_terminacion" name="motivo_terminacion" rows="4" 
                                  placeholder="Explique detalladamente el motivo de terminación del contrato..." 
                                  required minlength="8"></textarea>
                        <small class="form-text text-muted">Mínimo 8 caracteres</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Terminar Contrato</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
$(document).ready(function() {
    
    // Inicializar Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Seleccione una opción',
        allowClear: true
    });

    // Inicializar DataTable
    const table = $('#tablaContratos').DataTable({
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
        order: [[4, 'desc']], // Ordenar por fecha de inicio descendente
        columnDefs: [
            { orderable: false, targets: 8 } // Columna de acciones no ordenable
        ]
    });

    function validatePermanentContractDates(fechaInicio, fechaFin) {
      
        const start = moment(fechaInicio);
        const end = moment(fechaFin);
        
        const minEndDate = start.clone().add(2, 'months');
        
        if (end.isSameOrBefore(minEndDate, 'day')) {
            const fechaMinimaPermitida = minEndDate.add(1, 'day').format('DD/MM/YYYY');
            return { 
                valid: false, 
                message: `La fecha de finalización debe ser posterior a 2 meses.`
            };
        }
        
        return { valid: true };
    }

    function aplicarLogicaFechas() {
        const tipo = $('#tipo_contrato').val();
        const fechaInicioVal = $('#fecha_inicio').val();
        const $fechaFin = $('#fecha_fin');
        
        $('.campo-temporal').show();
        $fechaFin.prop('disabled', false).prop('readonly', false).prop('required', true);
        $fechaFin.removeClass('is-invalid').next('.invalid-feedback').remove(); 

        if (tipo === 'nombrado') {

            $('.campo-temporal').hide();
            $fechaFin.val('');
            
        } else if (tipo === 'temporal') {
            
            $fechaFin.prop('required', true); 
            
            if (fechaInicioVal) {
                const fechaFinCalculada = moment(fechaInicioVal).add(2, 'months').format('YYYY-MM-DD');
                $fechaFin.val(fechaFinCalculada);
                $fechaFin.prop('readonly', true);
            } else {
                $fechaFin.val('');
                $fechaFin.prop('readonly', false);
            }
            
        } else if (tipo === 'permanente') {

            $fechaFin.prop('readonly', false); 

            
            if ($('#fecha_inicio').val()=== '') {
                $('#fecha_fin').attr('onkeydown', 'return false');
            } else {
                $('#fecha_fin').attr('onkeydown', 'return true');
            }


            if (fechaInicioVal && $fechaFin.val()) {
                const validationResult = validatePermanentContractDates(fechaInicioVal, $fechaFin.val());

                if (!validationResult.valid) {
                    $fechaFin.addClass('is-invalid');
                    $fechaFin.next('.invalid-feedback').remove();
                    $fechaFin.after(`<div class="invalid-feedback">${validationResult.message}</div>`);
                } else {
                    $fechaFin.removeClass('is-invalid').next('.invalid-feedback').remove();
                }
            }
            
        } else {
            $fechaFin.val('');
        }
    }

    $('#tipo_contrato').off('change').on('change', aplicarLogicaFechas);
    $('#fecha_inicio').off('change').on('change', aplicarLogicaFechas);
    $('#fecha_inicio').off('change input').on('change input', aplicarLogicaFechas);
    $('#fecha_fin').off('change').on('change', function() {
    // Si es permanente y cambia la fecha fin, podemos pre-validar
        if ($('#tipo_contrato').val() === 'permanente') {
            aplicarLogicaFechas(); // Esto es opcional, pero asegura que se refresque el estado
        }
    });

    // Abrir modal para nuevo contrato
    $('#btnNuevo').click(function() {
        resetForm();
        $('#contrato_id').val('');
        $('#modalTitle').text('Nuevo Contrato');
        $('#activo').prop('checked', true);
        $('#tipo_contrato').val('').trigger('change');
        $('.campo-temporal').show();
        
        // Recargar select2 de personal disponible
        $.ajax({
            url: '{{ route("personal.contratos.personal-disponible") }}',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    let options = '<option value="">Seleccione un empleado</option>';
                    response.data.forEach(function(personal) {
                        options += `<option value="${personal.id}" data-dni="${personal.dni}" data-funcion="${personal.funcion}">
                            ${personal.nombre_completo} - ${personal.dni}
                        </option>`;
                    });
                    $('#personal_id').html(options).trigger('change');
                }
            }
        });
        
        $('#modalContrato').modal('show');
    });

    // Abrir modal para editar
    $(document).on('click', '.btnEditar', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/personal/contratos/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const contrato = response.data;
                    
                    $('#contrato_id').val(contrato.id);
                    $('#tipo_contrato').val(contrato.tipo_contrato).trigger('change');
                    $('#fecha_inicio').val(contrato.fecha_inicio);
                    $('#fecha_fin').val(contrato.fecha_fin || '');
                    $('#salario').val(contrato.salario || '');
                    $('#departamento_id').val(contrato.departamento_id).trigger('change');
                    $('#periodo_prueba').val(contrato.periodo_prueba || '');
                    $('#observaciones').val(contrato.observaciones || '');
                    $('#activo').prop('checked', contrato.activo);
                    
                    // Agregar el personal actual al select (aunque tenga contrato)
                    if (contrato.personal_id) {
                        const personalOption = `<option value="${contrato.personal_id}" selected>
                            ${contrato.personal_nombre}
                        </option>`;
                        $('#personal_id').html(personalOption).trigger('change');
                    }
                    
                    $('#modalTitle').text('Editar Contrato');
                    $('#modalContrato').modal('show');
                }
            },
            error: function(xhr) {
                showErrorAlert('Error al cargar los datos del contrato');
            }
        });
    });

    // Guardar (crear o actualizar)
    $('#formContrato').submit(function(e) {
        e.preventDefault();
        
        const id = $('#contrato_id').val();
        const url = id ? `/personal/contratos/${id}` : '/personal/contratos';
        const method = id ? 'PUT' : 'POST';
        
        const data = {
            personal_id: $('#personal_id').val(),
            tipo_contrato: $('#tipo_contrato').val(),
            fecha_inicio: $('#fecha_inicio').val(),
            fecha_fin: $('#tipo_contrato').val() === 'temporal' || 'permanente' ? $('#fecha_fin').val() : null,
            salario: $('#salario').val() || null,
            departamento_id: $('#departamento_id').val() || null,
            periodo_prueba: $('#periodo_prueba').val() || null,
            observaciones: $('#observaciones').val() || null,
            activo: $('#activo').is(':checked') ? 1 : 0
        };
        
        if (method === 'PUT') {
            data._method = 'PUT';
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    $('#modalContrato').modal('hide');
                    showSuccessAlert(response.message);
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors);
                } else {
                    showErrorAlert(xhr.responseJSON.message || 'Error al guardar el contrato');
                }
            }
        });
    });

    // Toggle activo/inactivo
    $(document).on('click', '.btnToggleActivo', function() {
        const id = $(this).data('id');
        const activo = $(this).data('activo');
        const mensaje = activo ? '¿Desea desactivar este contrato?' : '¿Desea activar este contrato?';
        
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
                    url: `/personal/contratos/${id}/toggle-activo`,
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

    // Terminar contrato
    $(document).on('click', '.btnTerminar', function() {
        const id = $(this).data('id');
        $('#contrato_terminar_id').val(id);
        const today = new Date();
            
            // Obtener componentes y asegurar formato YYYY-MM-DD
            const year = today.getFullYear();
            // getMonth() es 0-indexado, sumamos 1
            const month = String(today.getMonth() + 1).padStart(2, '0'); 
            const day = String(today.getDate()).padStart(2, '0');
            
            const todayFormatted = `${year}-${month}-${day}`;

            // Establecer la fecha de hoy en el campo
            $('#fecha_terminacion').val(todayFormatted);
        $('#motivo_terminacion').val('');
        $('#modalTerminar').modal('show');
    });

    // Procesar terminación de contrato
    $('#formTerminar').submit(function(e) {
        e.preventDefault();
        
        const id = $('#contrato_terminar_id').val();
        const data = {
            fecha_terminacion: $('#fecha_terminacion').val(),
            motivo_terminacion: $('#motivo_terminacion').val()
        };

        $.ajax({
            url: `/personal/contratos/${id}/terminar`,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    $('#modalTerminar').modal('hide');
                    showSuccessAlert(response.message);
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors);
                } else {
                    showErrorAlert(xhr.responseJSON.message || 'Error al terminar el contrato');
                }
            }
        });
    });

    // Eliminar
    $(document).on('click', '.btnEliminar', function() {
        const id = $(this).data('id');
        
        confirmDelete(function() {
            $.ajax({
                url: `/personal/contratos/${id}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        showSuccessAlert(response.message);
                        setTimeout(() => location.reload(), 1500);
                    }
                },
                error: function(xhr) {
                    showErrorAlert(xhr.responseJSON.message || 'Error al eliminar el contrato');
                }
            });
        });
    });

    // Función para resetear formulario
    function resetForm() {
        $('#formContrato')[0].reset();
        $('#formContrato .is-invalid').removeClass('is-invalid');
        $('#formContrato .invalid-feedback').remove();
        $('.select2').val(null).trigger('change');
    }

});
</script>
@endpush