@extends('layouts.app')

@section('title', 'Contratos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-file-contract text-primary"></i> Gestión de Contratos
                    </h2>
                    <p class="text-muted mb-0">Administración de contratos del personal</p>
                </div>
                <button class="btn btn-primary" id="btnNuevo">
                    <i class="fas fa-plus"></i> Nuevo Contrato
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
    
        {{-- <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Contratos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalContratos">
                                {{ $contratos->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Contratos Activos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $contratos->where('activo', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Nombrados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $contratos->where('tipo_contrato', 'nombrado')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-infinity fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Permanentes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $contratos->where('tipo_contrato', 'permanente')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Temporales
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $contratos->where('tipo_contrato', 'temporal')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <!-- Tabla de Contratos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Listado de Contratos
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tablaContratos" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Empleado</th>
                            <th>Función</th>
                            <th>Tipo</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th>Vigencia</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contratos as $contrato)
                        <tr>
                            <td>{{ $contrato->id }}</td>
                            <td>
                                <strong>{{ $contrato->personal->nombre_completo }}</strong><br>
                                <small class="text-muted">DNI: {{ $contrato->personal->dni }}</small>
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ $contrato->personal->funcion->nombre ?? 'Sin función' }}
                                </span>
                            </td>
                            <td>
                                @if($contrato->tipo_contrato === 'permanente')
                                    <span class="badge badge-info">
                                        <i class="fas fa-infinity"></i> Permanente
                                    </span>
                                @else
                                    <span class="badge badge-warning">
                                        <i class="fas fa-clock"></i> Temporal
                                    </span>
                                @endif
                            </td>
                            <td>{{ $contrato->fecha_inicio->format('d/m/Y') }}</td>
                            <td>
                                @if($contrato->fecha_fin)
                                    {{ $contrato->fecha_fin->format('d/m/Y') }}
                                    @if($contrato->dias_restantes !== null)
                                        <br>
                                        @if($contrato->dias_restantes > 30)
                                            <small class="text-success">{{ $contrato->dias_restantes }} días</small>
                                        @elseif($contrato->dias_restantes > 0)
                                            <small class="text-warning">{{ $contrato->dias_restantes }} días</small>
                                        @else
                                            <small class="text-danger">Vencido</small>
                                        @endif
                                    @endif
                                @else
                                    <span class="text-muted">Indefinido</span>
                                @endif
                            </td>
                            <td>
                                @if($contrato->activo)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Activo
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-times-circle"></i> Inactivo
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($contrato->esta_vigente)
                                    <span class="badge badge-success">Vigente</span>
                                @else
                                    <span class="badge badge-danger">No Vigente</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-info btnVer" 
                                            data-id="{{ $contrato->id }}"
                                            title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning btnEditar" 
                                            data-id="{{ $contrato->id }}"
                                            title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-{{ $contrato->activo ? 'secondary' : 'success' }} btnToggleActivo" 
                                            data-id="{{ $contrato->id }}"
                                            data-activo="{{ $contrato->activo ? '1' : '0' }}"
                                            title="{{ $contrato->activo ? 'Desactivar' : 'Activar' }}">
                                        <i class="fas fa-{{ $contrato->activo ? 'times' : 'check' }}"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btnEliminar" 
                                            data-id="{{ $contrato->id }}"
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

<!-- Modal Crear/Editar Contrato -->
<div class="modal fade" id="modalContrato" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- Header con color más oscuro -->
            <div class="modal-header" style="background-color: #1e88e5; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-file-contract"></i> <span id="modalContratoTitleText">Nuevo Contrato</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formContrato">
                <input type="hidden" id="contrato_id" name="contrato_id">
                <div class="modal-body">
                    <div class="row">
                        <!-- Empleado -->
                        <div class="col-md-12 mb-3">
                            <label for="personal_id">Empleado: <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="personal_id" name="personal_id" required>
                                <option value="">Seleccione un empleado</option>
                                @foreach(\App\Models\Personal::activos()->with('funcion')->orderBy('nombres')->get() as $p)
                                    <option value="{{ $p->id }}">{{ $p->nombre_completo }} - {{ $p->funcion->nombre ?? 'Sin función' }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Tipo de Contrato -->
                        <div class="col-md-12 mb-3">
                            <label for="tipo_contrato">Tipo de Contrato</label>
                            <select class="form-control" id="tipo_contrato" name="tipo_contrato" required>
                                <option value="">Seleccione tipo</option>
                                <option value="permanente">Permanente</option>
                                <option value="temporal">Temporal</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Fecha Inicio y Salario -->
                        <div class="col-md-6 mb-3">
                            <label for="fecha_inicio">Fecha de Inicio: <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="salario">Salario: <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="salario" name="salario" placeholder="0.00">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Departamento -->
                        <div class="col-md-12 mb-3">
                            <label for="departamento_id">Departamento: <span class="text-danger">*</span></label>
                            <select class="form-control" id="departamento_id" name="departamento_id">
                                <option value="">Seleccione un departamento</option>
                                @foreach($departamentos as $depto)
                                    <option value="{{ $depto->id }}">{{ $depto->nombre }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Fecha Fin -->
                        <div class="col-md-12 mb-3" id="fechaFinContainer">
                            <label for="fecha_fin">Fecha de Finalización:</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                            <small class="text-muted" id="fechaFinHelp">Dejar en blanco si es contrato indefinido</small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Período de Prueba -->
                        <div class="col-md-12 mb-3">
                            <label for="periodo_prueba">Período de Prueba (meses): <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="periodo_prueba" name="periodo_prueba" placeholder="3" min="0" max="12">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Contrato Activo -->
                        <div class="col-md-12 mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="activo" name="activo" checked>
                                <label class="custom-control-label" for="activo">¿Contrato Activo?</label>
                            </div>
                            <small class="text-muted d-block">Solo puede haber un contrato activo por empleado</small>
                        </div>

                        <!-- Motivo de Terminación -->
                        <div class="col-md-12 mb-3" id="motivoTerminacionContainer" style="display: none;">
                            <label for="motivo_terminacion">Motivo de Terminación:</label>
                            <textarea class="form-control" id="motivo_terminacion" name="motivo_terminacion" rows="3"></textarea>
                            <small class="text-muted">Solo aplica si el contrato no está activo</small>
                        </div>

                        <!-- Observaciones -->
                        <div class="col-md-12 mb-3">
                            <label for="observaciones">Observaciones:</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                            <small class="text-muted">Información adicional sobre el contrato</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Detalles -->
<div class="modal fade" id="modalVerContrato" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye"></i> Detalles del Contrato
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detallesContrato">
                <!-- Se llenará con JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-theme@0.1.0-beta.10/dist/select2-bootstrap.min.css" rel="stylesheet" />
<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    .select2-container {
        width: 100% !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Configurar CSRF Token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Inicializar DataTable EN ESPAÑOL
    const table = $('#tablaContratos').DataTable({
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
            },
            "aria": {
                "sortAscending": ": activar para ordenar la columna ascendente",
                "sortDescending": ": activar para ordenar la columna descendente"
            }
        },
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true
    });

    // Inicializar Select2
    $('.select2').select2({
        theme: 'bootstrap',
        placeholder: 'Seleccione una opción',
        allowClear: true,
        dropdownParent: $('#modalContrato')
    });

    // Mostrar/ocultar campos según estado del contrato
    $('#activo').change(function() {
        if ($(this).is(':checked')) {
            $('#motivoTerminacionContainer').hide();
            $('#motivo_terminacion').val('').prop('required', false);
        } else {
            $('#motivoTerminacionContainer').show();
            $('#motivo_terminacion').prop('required', false);
        }
    });

    // Mostrar/ocultar fecha fin según tipo de contrato
    $('#tipo_contrato').change(function() {
        const tipo = $(this).val();
        if (tipo === 'permanente') {
            $('#fechaFinContainer').hide();
            $('#fecha_fin').val('').prop('required', false);
            $('#fechaFinHelp').text('Los contratos permanentes no tienen fecha de finalización');
        } else if (tipo === 'temporal') {
            $('#fechaFinContainer').show();
            $('#fecha_fin').prop('required', true);
            $('#fechaFinHelp').text('Los contratos temporales duran 3 meses');
            
            const fechaInicio = $('#fecha_inicio').val();
            if (fechaInicio) {
                const inicio = new Date(fechaInicio);
                inicio.setMonth(inicio.getMonth() + 3);
                $('#fecha_fin').val(inicio.toISOString().split('T')[0]);
            }
        } else {
            $('#fechaFinContainer').show();
            $('#fecha_fin').prop('required', false);
        }
    });

    // Calcular fecha fin cuando cambia fecha inicio
    $('#fecha_inicio').change(function() {
        if ($('#tipo_contrato').val() === 'temporal') {
            const fechaInicio = $(this).val();
            if (fechaInicio) {
                const inicio = new Date(fechaInicio);
                inicio.setMonth(inicio.getMonth() + 3);
                $('#fecha_fin').val(inicio.toISOString().split('T')[0]);
            }
        }
    });

    // Nuevo Contrato
    $('#btnNuevo').click(function() {
        resetForm();
        $('#modalContratoTitleText').text('Nuevo Contrato');
        $('#contrato_id').val('');
        $('#modalContrato').modal('show');
    });

    // Ver Contrato
    $(document).on('click', '.btnVer', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/personal/contratos/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const c = response.data;
                    const html = `
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-user"></i> Empleado</h6>
                                <p class="mb-0"><strong>${c.personal_nombre}</strong></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-file-contract"></i> Tipo de Contrato</h6>
                                <p class="mb-0">
                                    ${c.tipo_contrato === 'permanente' ? 
                                        '<span class="badge badge-info">Permanente</span>' : 
                                        '<span class="badge badge-warning">Temporal</span>'}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-calendar-alt"></i> Fecha de Inicio</h6>
                                <p class="mb-0">${c.fecha_inicio}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-calendar-check"></i> Fecha de Fin</h6>
                                <p class="mb-0">${c.fecha_fin || '<span class="text-muted">Indefinido</span>'}</p>
                                ${c.dias_restantes !== null ? `<small class="text-muted">${c.dias_restantes} días restantes</small>` : ''}
                            </div>
                            ${c.salario ? `
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-money-bill"></i> Salario</h6>
                                <p class="mb-0">S/ ${parseFloat(c.salario).toFixed(2)}</p>
                            </div>
                            ` : ''}
                            ${c.departamento_nombre ? `
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-building"></i> Departamento</h6>
                                <p class="mb-0">${c.departamento_nombre}</p>
                            </div>
                            ` : ''}
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-toggle-on"></i> Estado</h6>
                                <p class="mb-0">
                                    ${c.activo ? 
                                        '<span class="badge badge-success">Activo</span>' : 
                                        '<span class="badge badge-secondary">Inactivo</span>'}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-check-circle"></i> Vigencia</h6>
                                <p class="mb-0">
                                    ${c.esta_vigente ? 
                                        '<span class="badge badge-success">Vigente</span>' : 
                                        '<span class="badge badge-danger">No Vigente</span>'}
                                </p>
                            </div>
                            ${c.observaciones ? `
                            <div class="col-md-12 mb-3">
                                <h6><i class="fas fa-comment"></i> Observaciones</h6>
                                <p class="mb-0">${c.observaciones}</p>
                            </div>
                            ` : ''}
                        </div>
                    `;
                    $('#detallesContrato').html(html);
                    $('#modalVerContrato').modal('show');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'No se pudieron cargar los detalles', 'error');
            }
        });
    });

    // Editar Contrato
    $(document).on('click', '.btnEditar', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/personal/contratos/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const c = response.data;
                    
                    $('#contrato_id').val(c.id);
                    $('#personal_id').val(c.personal_id).trigger('change');
                    $('#tipo_contrato').val(c.tipo_contrato).trigger('change');
                    $('#fecha_inicio').val(c.fecha_inicio);
                    $('#fecha_fin').val(c.fecha_fin || '');
                    $('#salario').val(c.salario || '');
                    $('#departamento_id').val(c.departamento_id || '');
                    $('#periodo_prueba').val(c.periodo_prueba || '');
                    $('#observaciones').val(c.observaciones || '');
                    $('#motivo_terminacion').val(c.motivo_terminacion || '');
                    $('#activo').prop('checked', c.activo);
                    
                    if (!c.activo) {
                        $('#motivoTerminacionContainer').show();
                    }
                    
                    $('#modalContratoTitleText').text('Editar Contrato');
                    $('#modalContrato').modal('show');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'No se pudo cargar el contrato', 'error');
            }
        });
    });

    // Guardar Contrato - CON ACTUALIZACIÓN DINÁMICA
    $('#formContrato').submit(function(e) {
        e.preventDefault();
        
        const id = $('#contrato_id').val();
        const url = id ? `/personal/contratos/${id}` : '/personal/contratos';
        const method = id ? 'PUT' : 'POST';
        
        const data = {
            personal_id: $('#personal_id').val(),
            tipo_contrato: $('#tipo_contrato').val(),
            fecha_inicio: $('#fecha_inicio').val(),
            fecha_fin: $('#fecha_fin').val() || null,
            salario: $('#salario').val(),
            departamento_id: $('#departamento_id').val(),
            periodo_prueba: $('#periodo_prueba').val(),
            observaciones: $('#observaciones').val(),
            motivo_terminacion: $('#motivo_terminacion').val(),
            activo: $('#activo').is(':checked')
        };
        
        $('#btnGuardar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function(response) {
                if (response.success) {
                    $('#modalContrato').modal('hide');
                    
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    if (id) {
                        actualizarFilaContrato(response.data);
                    } else {
                        agregarFilaContrato(response.data);
                    }
                    
                    $('#btnGuardar').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
                }
            },
            error: function(xhr) {
                $('#btnGuardar').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(key => {
                        $(`#${key}`).addClass('is-invalid');
                        $(`#${key}`).siblings('.invalid-feedback').text(errors[key][0]);
                    });
                } else {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Error al guardar el contrato', 'error');
                }
            }
        });
    });

    // Toggle Activo
    $(document).on('click', '.btnToggleActivo', function() {
        const id = $(this).data('id');
        const activo = $(this).data('activo') == '1';
        const mensaje = activo ? '¿Desactivar este contrato?' : '¿Activar este contrato?';
        
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
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Error al cambiar el estado', 'error');
                    }
                });
            }
        });
    });

    // Eliminar Contrato
    $(document).on('click', '.btnEliminar', function() {
        const id = $(this).data('id');
        const $row = $(this).closest('tr');
        
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
                    url: `/personal/contratos/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            table.row($row).remove().draw(false);
                            
                            Swal.fire({
                                icon: 'success',
                                title: '¡Eliminado!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Error al eliminar el contrato', 'error');
                    }
                });
            }
        });
    });

    // Reset Form
    function resetForm() {
        $('#formContrato')[0].reset();
        $('.select2').val(null).trigger('change');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#fechaFinContainer').show();
        $('#motivoTerminacionContainer').hide();
        $('#activo').prop('checked', true);
    }

    // Función para actualizar fila existente
    function actualizarFilaContrato(contrato) {
        const $row = $(`tr:has(button.btnEditar[data-id="${contrato.id}"])`);
        
        if ($row.length) {
            const tipoBadge = contrato.tipo_contrato === 'permanente' 
                ? '<span class="badge badge-info"><i class="fas fa-infinity"></i> Permanente</span>'
                : '<span class="badge badge-warning"><i class="fas fa-clock"></i> Temporal</span>';
            
            const estadoBadge = contrato.activo 
                ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>'
                : '<span class="badge badge-secondary"><i class="fas fa-times-circle"></i> Inactivo</span>';
            
            const vigenciaBadge = contrato.esta_vigente 
                ? '<span class="badge badge-success">Vigente</span>'
                : '<span class="badge badge-danger">No Vigente</span>';
            
            let fechaFinHtml = '<span class="text-muted">Indefinido</span>';
            if (contrato.fecha_fin) {
                const fechaFin = new Date(contrato.fecha_fin);
                fechaFinHtml = fechaFin.toLocaleDateString('es-PE');
                
                if (contrato.dias_restantes !== null) {
                    let colorClass = 'text-success';
                    if (contrato.dias_restantes <= 30) colorClass = 'text-warning';
                    if (contrato.dias_restantes <= 0) colorClass = 'text-danger';
                    
                    fechaFinHtml += `<br><small class="${colorClass}">${contrato.dias_restantes} días</small>`;
                }
            }
            
            const fechaInicio = new Date(contrato.fecha_inicio);
            const fechaInicioStr = fechaInicio.toLocaleDateString('es-PE');
            
            $row.find('td:eq(3)').html(tipoBadge);
            $row.find('td:eq(4)').text(fechaInicioStr);
            $row.find('td:eq(5)').html(fechaFinHtml);
            $row.find('td:eq(6)').html(estadoBadge);
            $row.find('td:eq(7)').html(vigenciaBadge);
            
            const $btnToggle = $row.find('.btnToggleActivo');
            $btnToggle.removeClass('btn-success btn-secondary')
                      .addClass(contrato.activo ? 'btn-secondary' : 'btn-success')
                      .attr('data-activo', contrato.activo ? '1' : '0')
                      .attr('title', contrato.activo ? 'Desactivar' : 'Activar')
                      .html(`<i class="fas fa-${contrato.activo ? 'times' : 'check'}"></i>`);
            
            $row.addClass('table-success');
            setTimeout(() => $row.removeClass('table-success'), 2000);
        }
    }

    // Función para agregar nueva fila
    function agregarFilaContrato(contrato) {
        const tipoBadge = contrato.tipo_contrato === 'permanente' 
            ? '<span class="badge badge-info"><i class="fas fa-infinity"></i> Permanente</span>'
            : '<span class="badge badge-warning"><i class="fas fa-clock"></i> Temporal</span>';
        
        const estadoBadge = contrato.activo 
            ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>'
            : '<span class="badge badge-secondary"><i class="fas fa-times-circle"></i> Inactivo</span>';
        
        const vigenciaBadge = contrato.esta_vigente 
            ? '<span class="badge badge-success">Vigente</span>'
            : '<span class="badge badge-danger">No Vigente</span>';
        
        let fechaFinHtml = '<span class="text-muted">Indefinido</span>';
        if (contrato.fecha_fin) {
            const fechaFin = new Date(contrato.fecha_fin);
            fechaFinHtml = fechaFin.toLocaleDateString('es-PE');
            
            if (contrato.dias_restantes !== null) {
                let colorClass = 'text-success';
                if (contrato.dias_restantes <= 30) colorClass = 'text-warning';
                if (contrato.dias_restantes <= 0) colorClass = 'text-danger';
                
                fechaFinHtml += `<br><small class="${colorClass}">${contrato.dias_restantes} días</small>`;
            }
        }
        
        const fechaInicio = new Date(contrato.fecha_inicio);
        const fechaInicioStr = fechaInicio.toLocaleDateString('es-PE');
        
        const newRow = `
            <tr class="table-success">
                <td>${contrato.id}</td>
                <td>
                    <strong>${contrato.personal.nombre_completo}</strong><br>
                    <small class="text-muted">DNI: ${contrato.personal.dni}</small>
                </td>
                <td>
                    <span class="badge badge-secondary">${contrato.personal.funcion?.nombre || 'Sin función'}</span>
                </td>
                <td>${tipoBadge}</td>
                <td>${fechaInicioStr}</td>
                <td>${fechaFinHtml}</td>
                <td>${estadoBadge}</td>
                <td>${vigenciaBadge}</td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-info btnVer" data-id="${contrato.id}" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning btnEditar" data-id="${contrato.id}" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-${contrato.activo ? 'secondary' : 'success'} btnToggleActivo" 
                                data-id="${contrato.id}" 
                                data-activo="${contrato.activo ? '1' : '0'}" 
                                title="${contrato.activo ? 'Desactivar' : 'Activar'}">
                            <i class="fas fa-${contrato.activo ? 'times' : 'check'}"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btnEliminar" data-id="${contrato.id}" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        
        table.row.add($(newRow)[0]).draw(false);
        
        setTimeout(() => {
            $(`tr:has(button.btnEditar[data-id="${contrato.id}"])`).removeClass('table-success');
        }, 2000);
    }
});
</script>

================================================================================
ENDOFFILE
cat /mnt/user-data/outputs/SCRIPT_COMPLETO_CONTRATOS.txt
Salida

Reemplaza TODO el contenido del <script> en tu archivo contratos/index.blade.php con esto:

================================================================================

<script>
$(document).ready(function() {
    // Configurar CSRF Token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Inicializar DataTable EN ESPAÑOL
    const table = $('#tablaContratos').DataTable({
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
            },
            "aria": {
                "sortAscending": ": activar para ordenar la columna ascendente",
                "sortDescending": ": activar para ordenar la columna descendente"
            }
        },
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true
    });

    // Inicializar Select2
    $('.select2').select2({
        theme: 'bootstrap',
        placeholder: 'Seleccione una opción',
        allowClear: true,
        dropdownParent: $('#modalContrato')
    });

    // Mostrar/ocultar campos según estado del contrato
    $('#activo').change(function() {
        if ($(this).is(':checked')) {
            $('#motivoTerminacionContainer').hide();
            $('#motivo_terminacion').val('').prop('required', false);
        } else {
            $('#motivoTerminacionContainer').show();
            $('#motivo_terminacion').prop('required', false);
        }
    });

    // Mostrar/ocultar fecha fin según tipo de contrato
    $('#tipo_contrato').change(function() {
        const tipo = $(this).val();
        if (tipo === 'permanente') {
            $('#fechaFinContainer').hide();
            $('#fecha_fin').val('').prop('required', false);
            $('#fechaFinHelp').text('Los contratos permanentes no tienen fecha de finalización');
        } else if (tipo === 'temporal') {
            $('#fechaFinContainer').show();
            $('#fecha_fin').prop('required', true);
            $('#fechaFinHelp').text('Los contratos temporales duran 3 meses');
            
            const fechaInicio = $('#fecha_inicio').val();
            if (fechaInicio) {
                const inicio = new Date(fechaInicio);
                inicio.setMonth(inicio.getMonth() + 3);
                $('#fecha_fin').val(inicio.toISOString().split('T')[0]);
            }
        } else {
            $('#fechaFinContainer').show();
            $('#fecha_fin').prop('required', false);
        }
    });

    // Calcular fecha fin cuando cambia fecha inicio
    $('#fecha_inicio').change(function() {
        if ($('#tipo_contrato').val() === 'temporal') {
            const fechaInicio = $(this).val();
            if (fechaInicio) {
                const inicio = new Date(fechaInicio);
                inicio.setMonth(inicio.getMonth() + 3);
                $('#fecha_fin').val(inicio.toISOString().split('T')[0]);
            }
        }
    });

    // Nuevo Contrato
    $('#btnNuevo').click(function() {
        resetForm();
        $('#modalContratoTitleText').text('Nuevo Contrato');
        $('#contrato_id').val('');
        $('#modalContrato').modal('show');
    });

    // Ver Contrato
    $(document).on('click', '.btnVer', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/personal/contratos/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const c = response.data;
                    const html = `
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-user"></i> Empleado</h6>
                                <p class="mb-0"><strong>${c.personal_nombre}</strong></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-file-contract"></i> Tipo de Contrato</h6>
                                <p class="mb-0">
                                    ${c.tipo_contrato === 'permanente' ? 
                                        '<span class="badge badge-info">Permanente</span>' : 
                                        '<span class="badge badge-warning">Temporal</span>'}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-calendar-alt"></i> Fecha de Inicio</h6>
                                <p class="mb-0">${c.fecha_inicio}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-calendar-check"></i> Fecha de Fin</h6>
                                <p class="mb-0">${c.fecha_fin || '<span class="text-muted">Indefinido</span>'}</p>
                                ${c.dias_restantes !== null ? `<small class="text-muted">${c.dias_restantes} días restantes</small>` : ''}
                            </div>
                            ${c.salario ? `
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-money-bill"></i> Salario</h6>
                                <p class="mb-0">S/ ${parseFloat(c.salario).toFixed(2)}</p>
                            </div>
                            ` : ''}
                            ${c.departamento_nombre ? `
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-building"></i> Departamento</h6>
                                <p class="mb-0">${c.departamento_nombre}</p>
                            </div>
                            ` : ''}
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-toggle-on"></i> Estado</h6>
                                <p class="mb-0">
                                    ${c.activo ? 
                                        '<span class="badge badge-success">Activo</span>' : 
                                        '<span class="badge badge-secondary">Inactivo</span>'}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6><i class="fas fa-check-circle"></i> Vigencia</h6>
                                <p class="mb-0">
                                    ${c.esta_vigente ? 
                                        '<span class="badge badge-success">Vigente</span>' : 
                                        '<span class="badge badge-danger">No Vigente</span>'}
                                </p>
                            </div>
                            ${c.observaciones ? `
                            <div class="col-md-12 mb-3">
                                <h6><i class="fas fa-comment"></i> Observaciones</h6>
                                <p class="mb-0">${c.observaciones}</p>
                            </div>
                            ` : ''}
                        </div>
                    `;
                    $('#detallesContrato').html(html);
                    $('#modalVerContrato').modal('show');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'No se pudieron cargar los detalles', 'error');
            }
        });
    });

    // Editar Contrato
    $(document).on('click', '.btnEditar', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/personal/contratos/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const c = response.data;
                    
                    $('#contrato_id').val(c.id);
                    $('#personal_id').val(c.personal_id).trigger('change');
                    $('#tipo_contrato').val(c.tipo_contrato).trigger('change');
                    $('#fecha_inicio').val(c.fecha_inicio);
                    $('#fecha_fin').val(c.fecha_fin || '');
                    $('#salario').val(c.salario || '');
                    $('#departamento_id').val(c.departamento_id || '');
                    $('#periodo_prueba').val(c.periodo_prueba || '');
                    $('#observaciones').val(c.observaciones || '');
                    $('#motivo_terminacion').val(c.motivo_terminacion || '');
                    $('#activo').prop('checked', c.activo);
                    
                    if (!c.activo) {
                        $('#motivoTerminacionContainer').show();
                    }
                    
                    $('#modalContratoTitleText').text('Editar Contrato');
                    $('#modalContrato').modal('show');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'No se pudo cargar el contrato', 'error');
            }
        });
    });

    // Guardar Contrato - VERSIÓN CORREGIDA
$('#formContrato').submit(function(e) {
    e.preventDefault();
    
    const id = $('#contrato_id').val();
    const url = id ? `/personal/contratos/${id}` : '/personal/contratos';
    
    // CAMBIO CRÍTICO: Usar POST con _method en lugar de PUT
    const data = {
        _method: id ? 'PUT' : 'POST',  // ← Agregar _method
        personal_id: $('#personal_id').val(),
        tipo_contrato: $('#tipo_contrato').val(),
        fecha_inicio: $('#fecha_inicio').val(),
        fecha_fin: $('#fecha_fin').val() || null,
        salario: $('#salario').val(),
        departamento_id: $('#departamento_id').val(),
        periodo_prueba: $('#periodo_prueba').val(),
        observaciones: $('#observaciones').val(),
        motivo_terminacion: $('#motivo_terminacion').val(),
        activo: $('#activo').is(':checked')
    };
    
    console.log('Enviando datos:', data);
    
    $('#btnGuardar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
    
    $.ajax({
        url: url,
        type: 'POST',  // ← SIEMPRE POST, Laravel lo convierte a PUT con _method
        data: data,
        dataType: 'json',  // ← Agregar dataType
        success: function(response) {
            console.log('Respuesta recibida:', response);
            
            if (response.success) {
                $('#modalContrato').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                
                if (id) {
                    actualizarFilaContrato(response.data);
                } else {
                    agregarFilaContrato(response.data);
                }
                
                $('#btnGuardar').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', {xhr, status, error});
            console.error('Respuesta del servidor:', xhr.responseText);
            
            $('#btnGuardar').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                Object.keys(errors).forEach(key => {
                    $(`#${key}`).addClass('is-invalid');
                    $(`#${key}`).siblings('.invalid-feedback').text(errors[key][0]);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Error al guardar el contrato'
                });
            }
        }
    });
});
    // Toggle Activo
    $(document).on('click', '.btnToggleActivo', function() {
        const id = $(this).data('id');
        const activo = $(this).data('activo') == '1';
        const mensaje = activo ? '¿Desactivar este contrato?' : '¿Activar este contrato?';
        
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
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Error al cambiar el estado', 'error');
                    }
                });
            }
        });
    });

    // Eliminar Contrato
    $(document).on('click', '.btnEliminar', function() {
        const id = $(this).data('id');
        const $row = $(this).closest('tr');
        
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
                    url: `/personal/contratos/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            table.row($row).remove().draw(false);
                            
                            Swal.fire({
                                icon: 'success',
                                title: '¡Eliminado!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Error al eliminar el contrato', 'error');
                    }
                });
            }
        });
    });

    // Reset Form
    function resetForm() {
        $('#formContrato')[0].reset();
        $('.select2').val(null).trigger('change');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#fechaFinContainer').show();
        $('#motivoTerminacionContainer').hide();
        $('#activo').prop('checked', true);
    }

    // Función para actualizar fila existente
    function actualizarFilaContrato(contrato) {
        const $row = $(`tr:has(button.btnEditar[data-id="${contrato.id}"])`);
        
        if ($row.length) {
            const tipoBadge = contrato.tipo_contrato === 'permanente' 
                ? '<span class="badge badge-info"><i class="fas fa-infinity"></i> Permanente</span>'
                : '<span class="badge badge-warning"><i class="fas fa-clock"></i> Temporal</span>';
            
            const estadoBadge = contrato.activo 
                ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>'
                : '<span class="badge badge-secondary"><i class="fas fa-times-circle"></i> Inactivo</span>';
            
            const vigenciaBadge = contrato.esta_vigente 
                ? '<span class="badge badge-success">Vigente</span>'
                : '<span class="badge badge-danger">No Vigente</span>';
            
            let fechaFinHtml = '<span class="text-muted">Indefinido</span>';
            if (contrato.fecha_fin) {
                const fechaFin = new Date(contrato.fecha_fin);
                fechaFinHtml = fechaFin.toLocaleDateString('es-PE');
                
                if (contrato.dias_restantes !== null) {
                    let colorClass = 'text-success';
                    if (contrato.dias_restantes <= 30) colorClass = 'text-warning';
                    if (contrato.dias_restantes <= 0) colorClass = 'text-danger';
                    
                    fechaFinHtml += `<br><small class="${colorClass}">${contrato.dias_restantes} días</small>`;
                }
            }
            
            const fechaInicio = new Date(contrato.fecha_inicio);
            const fechaInicioStr = fechaInicio.toLocaleDateString('es-PE');
            
            $row.find('td:eq(3)').html(tipoBadge);
            $row.find('td:eq(4)').text(fechaInicioStr);
            $row.find('td:eq(5)').html(fechaFinHtml);
            $row.find('td:eq(6)').html(estadoBadge);
            $row.find('td:eq(7)').html(vigenciaBadge);
            
            const $btnToggle = $row.find('.btnToggleActivo');
            $btnToggle.removeClass('btn-success btn-secondary')
                      .addClass(contrato.activo ? 'btn-secondary' : 'btn-success')
                      .attr('data-activo', contrato.activo ? '1' : '0')
                      .attr('title', contrato.activo ? 'Desactivar' : 'Activar')
                      .html(`<i class="fas fa-${contrato.activo ? 'times' : 'check'}"></i>`);
            
            $row.addClass('table-success');
            setTimeout(() => $row.removeClass('table-success'), 2000);
        }
    }

    // Función para agregar nueva fila
    function agregarFilaContrato(contrato) {
        const tipoBadge = contrato.tipo_contrato === 'permanente' 
            ? '<span class="badge badge-info"><i class="fas fa-infinity"></i> Permanente</span>'
            : '<span class="badge badge-warning"><i class="fas fa-clock"></i> Temporal</span>';
        
        const estadoBadge = contrato.activo 
            ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>'
            : '<span class="badge badge-secondary"><i class="fas fa-times-circle"></i> Inactivo</span>';
        
        const vigenciaBadge = contrato.esta_vigente 
            ? '<span class="badge badge-success">Vigente</span>'
            : '<span class="badge badge-danger">No Vigente</span>';
        
        let fechaFinHtml = '<span class="text-muted">Indefinido</span>';
        if (contrato.fecha_fin) {
            const fechaFin = new Date(contrato.fecha_fin);
            fechaFinHtml = fechaFin.toLocaleDateString('es-PE');
            
            if (contrato.dias_restantes !== null) {
                let colorClass = 'text-success';
                if (contrato.dias_restantes <= 30) colorClass = 'text-warning';
                if (contrato.dias_restantes <= 0) colorClass = 'text-danger';
                
                fechaFinHtml += `<br><small class="${colorClass}">${contrato.dias_restantes} días</small>`;
            }
        }
        
        const fechaInicio = new Date(contrato.fecha_inicio);
        const fechaInicioStr = fechaInicio.toLocaleDateString('es-PE');
        
        const newRow = `
            <tr class="table-success">
                <td>${contrato.id}</td>
                <td>
                    <strong>${contrato.personal.nombre_completo}</strong><br>
                    <small class="text-muted">DNI: ${contrato.personal.dni}</small>
                </td>
                <td>
                    <span class="badge badge-secondary">${contrato.personal.funcion?.nombre || 'Sin función'}</span>
                </td>
                <td>${tipoBadge}</td>
                <td>${fechaInicioStr}</td>
                <td>${fechaFinHtml}</td>
                <td>${estadoBadge}</td>
                <td>${vigenciaBadge}</td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-info btnVer" data-id="${contrato.id}" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning btnEditar" data-id="${contrato.id}" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-${contrato.activo ? 'secondary' : 'success'} btnToggleActivo" 
                                data-id="${contrato.id}" 
                                data-activo="${contrato.activo ? '1' : '0'}" 
                                title="${contrato.activo ? 'Desactivar' : 'Activar'}">
                            <i class="fas fa-${contrato.activo ? 'times' : 'check'}"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btnEliminar" data-id="${contrato.id}" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        
        table.row.add($(newRow)[0]).draw(false);
        
        setTimeout(() => {
            $(`tr:has(button.btnEditar[data-id="${contrato.id}"])`).removeClass('table-success');
        }, 2000);
    }
});
</script>
@endpush






