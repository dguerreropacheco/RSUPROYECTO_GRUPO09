{{-- VacacionesNuevo --}}
@extends('layouts.app')

@push('styles')
<style>
    .badge-pendiente {
        background-color: #ffc107;
    }

    .badge-aprobado {
        background-color: #58dc9aff;
    }

    .badge-completado {
        background-color: #12a0e7ff;
    }

    .badge-rechazado {
        background-color: red;
    }

    .badge-cancelado {
        background-color: red;
    }

    .modal-header {
        background-color: #3f6791;
        color: #fff;
    }

    .modal-header .btn-close,
    .modal-header .close span {
        color: #fff !important;
    }

</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Vacaciones</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item">Gestión de Empleados</li>
                    <li class="breadcrumb-item active">Vacaciones</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Vacaciones</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
                        <i class="fas fa-plus"></i> Nuevo Periodo
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="tablaVacaciones" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Días solicitados</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($periodos as $p)
                        <tr>
                            <td>{{ $p->vacaciones->personal->nombre_completo ?? '—' }}</td>
                            <td>{{ $p->fecha_inicio->format('d/m/Y') }}</td>
                            <td>{{ $p->fecha_fin->format('d/m/Y') }}</td>
                            <td>{{ $p->dias_utilizados }}</td>
                            <td><span class="badge badge-{{ $p->estado }}">{{ ucfirst($p->estado) }}</span></td>
                            <td>{{ $p->observaciones ?? '—' }}</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-info btn-sm btnEditar" data-id="{{ $p->id }}"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm btnEliminar" data-id="{{ $p->id }}"><i class="fas fa-trash"></i></button>
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

<div class="modal fade" id="modalVacaciones" tabindex="-1" role="dialog" aria-labelledby="tituloVacaciones" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="tituloVacaciones">Solicitud de Vacaciones</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formVacaciones" action="{{ route('personal.vacaciones.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="periodo_id" name="periodo_id">

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="personal_id">Empleado <span class="text-danger">*</span></label>
                                <select name="personal_id" id="personal_id" class="form-control select2" required>
                                    <option value="">Seleccione empleado</option>
                                    @foreach($personal as $p)
                                    <option value="{{ $p->id }}" data-dias-disponibles="{{ $p->dias_disponibles }}"  data-fecha-fin-contrato="{{ optional($p->contratoActivo)->fecha_fin }}">{{ $p->nombre_con_dias }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label>Fecha inicio</label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
                                <small style="color:gray;"> Las solicitudes deben hacerse con 10 días de anticipación </small>
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4">

                            <div class="form-group">
                                <label>Días solicitados</label>
                                <input type="number" name="dias_utilizados" id="dias_utilizados" class="form-control" min="1" max="30" **maxlength="2" ** required>
                                {{-- <small style="color:gray;"> El máximo es en base a días disponibles </small> --}}

                            </div>
                        </div>

                        <div class="col-md-4">

                            <div class="form-group">
                                <label>Fecha final</label>
                                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required readonly>
                                <small style="color:gray;"> Calculada automáticamente </small>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-group">
                                <label>Días disponibles</label>
                                <input type="number" name="dias_disponibles" id="dias_disponibles" class="form-control" readonly>
                                <small style="color:gray;"> Basado en contrato del empleado </small>

                            </div>

                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6">

                            <div class="form-group">
                                <label>Estado</label>
                                <select name="estado" id="estado" class="form-control">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="aprobado">Aprobado</option>
                                    <option value="rechazado">Rechazado</option>
                                    <option value="cancelado">Cancelado</option>
                                    <option value="completado">Completado</option>
                                </select>
                            </div>


                        </div>

                        <div class="col-md-6">


                            <div class="form-group">
                                <label>Notas</label>
                                <textarea name="observaciones" id="observaciones" rows="2" class="form-control"></textarea>
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
@endsection


@push('scripts')
<script>
    $(function() {
        // Inicialización de DataTables y CRUD
        const tabla = $('#tablaVacaciones').DataTable({
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
        }
            , order: [
                [1, 'desc']
            ]
        });

        $('#btnNuevo').click(function() {
            $('#formVacaciones')[0].reset();
            $('#periodo_id').val('');
            $('#personal_id').prop('disabled', false).val('').trigger('change');
            $('#modalTitle').text('Nuevo Periodo');
            $('#modalVacaciones').modal('show');
            setFechaInicioHoy();


            const personalId = $('#personal_id').val();
            const fechaInicial = $('#fecha_inicio').val();
            if (fechaInicial) {
                const anioInicial = new Date(fechaInicial).getFullYear();

                if (personalId) {
                    actualizarDiasDisponiblesRapido(personalId, anioInicial);
                } else {

                    actualizarOpcionesSelectPorAnio(anioInicial);
                }
            }
        });


        $(document).on('click', '.btnEditar', function() {
            const id = $(this).data('id');
            $.get(`/personal/vacaciones/${id}`, function(res) {
                if (res.success) {
                    const v = res.data;
                    $('#periodo_id').val(v.id);

                    $('#personal_id').val(v.vacaciones.personal_id).prop('disabled', true);

                    $('#fecha_inicio').val(v.fecha_inicio);
                    $('#fecha_fin').val(v.fecha_fin);

                    $('#dias_utilizados').val(v.dias_utilizados);

                    const anioEditar = new Date(v.fecha_inicio).getFullYear();
                    const personalIdEditar = v.vacaciones.personal_id;

                    window.fetchDisponibles(personalIdEditar, anioEditar, (dias) => {

                        $('#dias_disponibles').val(dias);
                        $('#dias_utilizados').attr('max', dias);

                        const selectedOption = $('#personal_id option:selected');
                        const nombreBase = selectedOption.text().split('(')[0].trim();
                        selectedOption.text(`${nombreBase} (${dias} días disponibles)`);
                    });

                    $('#estado').val(v.estado);
                    $('#observaciones').val(v.observaciones);

                    $('#modalTitle').text('Editar Vacaciones');
                    $('#modalVacaciones').modal('show');
                }
            });
        });

        $('#formVacaciones').submit(function(e) {
            e.preventDefault();
            const id = $('#periodo_id').val();
            const method = id ? 'PUT' : 'POST';
            const url = id ? `/personal/vacaciones/${id}` : '/personal/vacaciones';

            $.ajax({
                url
                , type: method
                , data: $(this).serialize()
                , success: function(res) {
                    if (res.success) {
                        $('#modalVacaciones').modal('hide');
                        Swal.fire('Éxito', res.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                }
                , error: function(xhr) {
                    let msg = 'No se pudo guardar';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        });

        $(document).on('click', '.btnEliminar', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: '¿Eliminar?'
                , text: 'Esta acción no se puede deshacer'
                , icon: 'warning'
                , showCancelButton: true
                , confirmButtonText: 'Sí, eliminar'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/personal/vacaciones/${id}`
                        , type: 'DELETE'
                        , success: res => {
                            if (res.success) {
                                Swal.fire('Eliminado', res.message, 'success');
                                setTimeout(() => location.reload(), 1000);
                            }
                        }
                        , error: () => Swal.fire('Error', 'No se pudo eliminar', 'error')
                    });
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {

        const personalSelect = document.getElementById('personal_id');
        const diasDisponiblesInput = document.getElementById('dias_disponibles');
        const diasSolicitadosInput = document.getElementById('dias_utilizados');
        const fechaInicioInput = document.getElementById('fecha_inicio');
        const fechaFinInput = document.getElementById('fecha_fin');


        function calcularFechaFin() {
            const dias = parseInt(diasSolicitadosInput.value, 11);
            const fechaInicioStr = fechaInicioInput.value;

            if (!isNaN(dias) && fechaInicioStr) {
                const fechaInicio = new Date(fechaInicioStr + 'T00:00:00');
                const anioInicio = fechaInicio.getFullYear();

                const fechaFin = new Date(fechaInicio);
                fechaFin.setDate(fechaInicio.getDate() + dias);

                fechaFin.setDate(fechaFin.getDate() - 1);

                const anioFin = fechaFin.getFullYear();

                if (anioFin > anioInicio) {
                    Swal.fire({
                        icon: 'warning'
                        , title: 'Atención'
                        , text: 'Las vacaciones de este año no pueden extenderse al siguiente.'
                        , confirmButtonText: 'Cerrar'
                        , confirmButtonColor: '#3085d6'
                    }).then(() => {
                        diasSolicitadosInput.value = '';
                        fechaFinInput.value = '';
                    });
                    return;
                }

                const yyyy = fechaFin.getFullYear();
                const mm = String(fechaFin.getMonth() + 1).padStart(2, '0');
                const dd = String(fechaFin.getDate()).padStart(2, '0');

                fechaFinInput.value = `${yyyy}-${mm}-${dd}`;
            } else {
                fechaFinInput.value = '';
            }
        }

        fechaInicioInput.addEventListener('input', calcularFechaFin);
        fechaInicioInput.addEventListener('change', calcularFechaFin);


        // Función de Fetch
        window.fetchDisponibles = function(personalId, anio, callback) {
            if (!personalId) {
                callback(0);
                return;
            }

            return fetch(`/personal/vacaciones/disponibles/${personalId}/${anio}`)
                .then(res => res.json())
                .then(data => {
                    const dias = data.success ? data.disponibles : 0;
                    callback(dias);
                    return dias;
                })
                .catch(err => {
                    console.error('Error de red al obtener días disponibles:', err);
                    callback(0);
                    return 0;
                });
        }

        window.actualizarOpcionesSelectPorAnio = function(nuevoAnio) {
            if (!nuevoAnio) return;

            const opciones = personalSelect.options;

            for (let i = 0; i < opciones.length; i++) {
                const option = opciones[i];
                const personalId = option.value;

                if (!personalId) {
                    continue;
                }

                const match = option.textContent.match(/([^()]+)\s*(\(|$)/);
                const nombreBase = match ? match[1].trim() : option.textContent.trim();

                option.textContent = `${nombreBase} (Cargando...)`;

                window.fetchDisponibles(personalId, nuevoAnio, (dias) => {
                    option.textContent = `${nombreBase} (${dias} días disponibles)`;
                });
            }
        }

        function actualizarDiasDisponiblesRapido(personalId, anio) {
            if (!personalId || !anio) {
                diasDisponiblesInput.value = '';
                diasSolicitadosInput.max = 0;
                return;
            }

            diasDisponiblesInput.type = 'text';
            diasDisponiblesInput.value = 'Cargando...';
            diasSolicitadosInput.max = 0;

            window.fetchDisponibles(personalId, anio, (dias) => {

                // 1. Carga RÁPIDA: Actualiza el input y el máximo
                diasDisponiblesInput.type = 'number';
                diasDisponiblesInput.value = dias;
                diasSolicitadosInput.max = dias;

                // 2. Actualiza el texto del SELECT solo para el empleado actual
                const option = personalSelect.options[personalSelect.selectedIndex];
                if (option) {
                    const nombreBase = option.textContent.split('(')[0].trim();
                    option.textContent = `${nombreBase} (${dias} días disponibles)`;
                }

                // 3. Resetea días solicitados si excede el nuevo máximo
                if (parseInt(diasSolicitadosInput.value) > dias) {
                    diasSolicitadosInput.value = '';
                    calcularFechaFin();
                }
            });

            // 4. Inicia la actualización LENTA de todos los demás empleados en segundo plano
            actualizarOpcionesSelectPorAnio(anio);
        }


        // --- EVENT LISTENERS ---

        // 1. Al cambiar la FECHA DE INICIO (para capturar el cambio de AÑO)
        fechaInicioInput.addEventListener('change', function() {
            const fechaInicioStr = this.value;
            const personalId = personalSelect.value;
            const option = personalSelect.options[personalSelect.selectedIndex];
            const fechaFinContrato = option ?.getAttribute('data-fecha-fin-contrato');

            if (fechaInicioStr) {
                const fechaInicio = new Date(fechaInicioStr + 'T00:00:00');
                const nuevoAnio = fechaInicio.getFullYear();

                if (fechaFinContrato) {
                    const finContrato = new Date(fechaFinContrato + 'T00:00:00');
                    if (fechaInicio > finContrato) {
                        Swal.fire({
                            icon: 'warning'
                            , title: 'No permitido'
                            , text: 'La fecha seleccionada supera el fin del contrato.'
                        });
                        this.value = '';
                        fechaFinInput.value = '';
                        return;
                    }
                }

                if (personalId) {
                    actualizarDiasDisponiblesRapido(personalId, nuevoAnio);
                } else {
                    actualizarOpcionesSelectPorAnio(nuevoAnio);
                }
            }

            calcularFechaFin();
        });


        // 2. Al cambiar el EMPLEADO
        personalSelect.addEventListener('change', function() {
            $('#dias_utilizados').val('');
            $('#fecha_fin').val('');

            const personalId = this.value;
            const fechaInicioStr = fechaInicioInput.value;

            if (personalId && fechaInicioStr) {
                const anio = new Date(fechaInicioStr).getFullYear();
                actualizarDiasDisponiblesRapido(personalId, anio);
            } else {
                diasDisponiblesInput.value = '';
                diasSolicitadosInput.max = 0;
            }
        });

        // 3. Al escribir DÍAS SOLICITADOS (Validaciones)
        diasSolicitadosInput.addEventListener('input', function() {
            const maxDays = parseInt(this.max);
            if (parseInt(this.value) > maxDays) {
                this.value = maxDays;
            }
            calcularFechaFin();
        });

        // **VALIDACIÓN** para no escribir más de 2 dígitos (teclado)
        diasSolicitadosInput.addEventListener('keydown', function(e) {
            // Permitir teclas especiales (backspace, delete, flechas, ctrl/meta)
            if ([8, 9, 37, 39, 46].includes(e.keyCode) || e.ctrlKey || e.metaKey) {
                return;
            }

            // Si ya hay 2 dígitos y es un número o cualquier otra tecla, prevenir la entrada
            if (this.value.length >= 2) {
                e.preventDefault();
            }
        });

        function setFechaInicioHoy() {
            const hoy = new Date();
            hoy.setDate(hoy.getDate() + 11);
            const yyyy = hoy.getFullYear();
            const mm = String(hoy.getMonth() + 1).padStart(2, '0');
            const dd = String(hoy.getDate()).padStart(2, '0');
            const fechaHoy = `${yyyy}-${mm}-${dd}`;

            fechaInicioInput.min = fechaHoy;

            if (!fechaInicioInput.value) {
                fechaInicioInput.value = fechaHoy;
            }
        }

        setFechaInicioHoy();


    });

    function setFechaInicioHoy() {
        const hoy = new Date();
        hoy.setDate(hoy.getDate() + 11);
        const yyyy = hoy.getFullYear();
        const mm = String(hoy.getMonth() + 1).padStart(2, '0');
        const dd = String(hoy.getDate()).padStart(2, '0');
        const fechaHoy = `${yyyy}-${mm}-${dd}`;

        $('#fecha_inicio').attr('min', fechaHoy).val(fechaHoy);
    }

    $('#personal_id').on('change', function() {
        $('#dias_utilizados').val('');
        $('#fecha_fin').val('');
    });

    $(document).ready(function() {
        $('#btnNuevo').on('click', function() {
            setFechaInicioHoy();
            $('#fecha_fin').val('');
            $('#dias_utilizados').val('');
        });
    });

</script>
@endpush
