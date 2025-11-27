{{-- turnos/index.blade.php --}}
@extends('layouts.app')


@push('styles')
<style>
    .badge-pendiente {
        background-color: #ffc107;
    }

    .badge-aprobado {
        background-color: #58dc9aff;
    }

    /* Usaremos esta para el estado azul solicitado */
    .badge-completado {
        background-color: #12a0e7ff;
        color: white; /* Aseguramos que el texto sea blanco */
    }

    .badge-rechazado {
        background-color: red;
    }

    .badge-cancelado {
        background-color: red;
    }
    
    /* Clase explícita para el color azul solicitado */
    .badge-blue {
        background-color: #12a0e7ff; /* Color azul fuerte */
        color: white;
    }

    .modal-header {
        background-color: #3f6791;
        color: #fff;
    }

    .modal-header .btn-close,
    .modal-header .close span {
        color: #fff !important;
    }
    
    /* Solución para asegurar los bordes en los campos del formulario (Selects e Inputs) */
    #modalTurno .form-control {
        border: 1px solid #ced4da !important;
    }

</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Turnos</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Turnos</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Turnos</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
                        <i class="fas fa-plus"></i> Nuevo Turno
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="tablaTurnos" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Hora de entrada</th>
                            <th>Hora de salida</th>
                            <th>Descripción</th>
                            {{-- Nuevo encabezado de columna para el Estado --}}
                            <th>Estado</th> 
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($turnos as $turno)
                        <tr>
                            <td>{{ $turno->name }}</td>
                            <td>{{ $turno->hour_in }}</td>
                            <td>{{ $turno->hour_out }}</td>
                            <td>{{ $turno->description ?? '—' }}</td>
                            {{-- Columna de Estado con badge azul como solicitado --}}
                            <td>
                                <span class="badge badge-blue">ACTIVO</span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-info btn-sm btnEditar" data-id="{{ $turno->id }}"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm btnEliminar" data-id="{{ $turno->id }}"><i class="fas fa-trash"></i></button>
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

{{-- Modal Nuevo Turno --}}
<div class="modal fade" id="modalTurno" tabindex="-1" role="dialog" aria-labelledby="tituloModalTurno" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header"> {{-- Usamos bg-primary para un estilo similar --}}
                <h5 class="modal-title" id="tituloModalTurno">Nuevo Turno</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formTurno" action="{{ route('turnos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="turno_id" name="turno_id">

                    <div class="form-group">
                        <label for="name">Nombre del Turno <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Ingrese el nombre del turno" required>
                        <small class="form-text text-muted">Ejemplo: Turno Mañana, Turno Tarde, Turno Noche</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hour_in">Hora de Entrada <span class="text-danger">*</span></label>
                                {{-- Tipo 'time' para el selector nativo, aunque la BD use varchar --}}
                                <input type="time" name="hour_in" id="hour_in" class="form-control" required>
                                <small class="form-text text-muted">Formato de 24 horas</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hour_out">Hora de Salida <span class="text-danger">*</span></label>
                                <input type="time" name="hour_out" id="hour_out" class="form-control" required>
                                <small class="form-text text-muted">Formato de 24 horas</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea name="description" id="description" rows="3" class="form-control" placeholder="Ingrese una descripción del turno (opcional)"></textarea>
                        <small class="form-text text-muted">Descripción de las características del turno</small>
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
        // Inicialización de DataTables
        const tabla = $('#tablaTurnos').DataTable({
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
        });

        // Evento para Abrir Modal de Nuevo Turno
        $('#btnNuevo').click(function() {
            $('#formTurno')[0].reset();
            $('#turno_id').val('');
            $('#tituloModalTurno').text('Nuevo Turno');
            $('#modalTurno').modal('show');
        });

        // Evento para Editar Turno
        $(document).on('click', '.btnEditar', function() {
            const id = $(this).data('id');
            $('#tituloModalTurno').text('Editar Turno');
            
            $.get(`/turnos/${id}/show`, function(res) {
                if (res.success) {
                    const t = res.data;
                    $('#turno_id').val(t.id);
                    $('#name').val(t.name);
                    $('#hour_in').val(t.hour_in);
                    $('#hour_out').val(t.hour_out);
                    $('#description').val(t.description);
                    $('#modalTurno').modal('show');
                }
            });
        });

        // Evento para Guardar/Actualizar Turno
        $('#formTurno').submit(function(e) {
            e.preventDefault();
            const id = $('#turno_id').val();
            const url = id ? `/turnos/${id}/update` : '/turnos/store'; // Usar rutas RESTful
            const method = 'POST'; // Laravel usa POST y simula PUT/PATCH

            $.ajax({
                url: url
                , type: method
                , data: $(this).serialize() + (id ? '&_method=PUT' : '')
                , success: function(res) {
                    if (res.success) {
                        $('#modalTurno').modal('hide');
                        Swal.fire('Éxito', res.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                }
                , error: function(xhr) {
                    let msg = 'Error al guardar el turno.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        // Aquí puedes mostrar errores de validación
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        });

        // Evento para Eliminar Turno
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
                        url: `/turnos/${id}/destroy`
                        , type: 'POST'
                        , data: {
                            _token: '{{ csrf_token() }}'
                            , _method: 'DELETE' // Simular el método DELETE
                        }
                        , success: res => {
                            if (res.success) {
                                Swal.fire('Eliminado', res.message, 'success');
                                setTimeout(() => location.reload(), 1000);
                            }
                        }
                        , error: () => Swal.fire('Error', 'No se pudo eliminar el turno', 'error')
                    });
                }
            });
        });
    });
</script>
@endpush