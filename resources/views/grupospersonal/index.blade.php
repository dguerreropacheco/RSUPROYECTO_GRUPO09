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
.badge-rechazado, .badge-cancelado {
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
 
#modalGrupo .form-control {
    border: 1px solid #ced4da !important;
}

</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestion de Grupos de Personal</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Grupos de Personal</li>
                </ol>
            </div>
        </div>
        
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Grupos</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevo">
                        <i class="fas fa-users"></i> Nuevo Grupo
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="tablaGrupos" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Zona</th>
                            <th>Turno</th>
                            <th>Vehiculo</th>
                            <th>Dias</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grupos as $grupo)
                        <tr>
                            <td>{{ $grupo->nombre }}</td>
                            <td>{{ $grupo->zona->nombre ?? '-' }}</td>
                            <td>{{ $grupo->turno->name ?? '-' }}</td>
                            <td>{{ $grupo->vehiculo->codigo ?? '-' }}</td>
                            <td>{{ str_replace(',', ', ', $grupo->dias) }}</td>
                            <td>
                                @php
                                    $claseEstado = $grupo->estado == 1 ? 'success' : 'danger'; 
                                @endphp
                                <span class="badge badge-{{ $claseEstado }}">{{ $grupo->estado_label }}</span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-info btn-sm btnEditar" data-id="{{ $grupo->id }}"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm btnEliminar" data-id="{{ $grupo->id }}"><i class="fas fa-trash"></i></button>
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

<div class="modal fade" id="modalGrupo" tabindex="-1" role="dialog" aria-labelledby="tituloModalGrupo" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header"> 
                <h5 class="modal-title" id="tituloModalGrupo">Nuevo Grupo de Personal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formGrupo" action="{{ route('grupospersonal.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="grupo_id" name="grupo_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">Nombre del grupo <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre" class="form-control" placeholder="GRUPO ZONA A" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="zona_id">Zona <span class="text-danger">*</span></label>
                                <select name="zona_id" id="zona_id" class="form-control" required>
                                    <option value="">Seleccione Zona</option>
                                    @foreach($zonas as $zona)
                                        <option value="{{ $zona->id }}">{{ $zona->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="turno_id">Turno <span class="text-danger">*</span></label>
                                <select name="turno_id" id="turno_id" class="form-control" required>
                                    <option value="">Seleccione Turno</option>
                                    @foreach($turnos as $turno)
                                        <option value="{{ $turno->id }}">{{ $turno->name }} ({{ $turno->hour_in }} - {{ $turno->hour_out }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vehiculo_id">Vehiculo <span class="text-danger">*</span></label>
                                <select name="vehiculo_id" id="vehiculo_id" class="form-control" required>
                                    <option value="">Seleccione Vehiculo</option>
                                    @foreach($vehiculos as $vehiculo)
                                        <option value="{{ $vehiculo->id }}">{{ $vehiculo->codigo }} (Capacidad: {{ $vehiculo->capacidad ?? 'N/A' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Dias de trabajo <span class="text-danger">*</span></label>
                        <div class="row px-2">
                            <div class="col-md-2 custom-control custom-checkbox">
                                <input type="checkbox" name="dias[]" id="dia_lunes" value="Lunes" class="custom-control-input">
                                <label class="custom-control-label" for="dia_lunes">Lunes</label>
                            </div>
                            <div class="col-md-2 custom-control custom-checkbox">
                                <input type="checkbox" name="dias[]" id="dia_martes" value="Martes" class="custom-control-input">
                                <label class="custom-control-label" for="dia_martes">Martes</label>
                            </div>
                            <div class="col-md-2 custom-control custom-checkbox">
                                <input type="checkbox" name="dias[]" id="dia_miercoles" value="Miércoles" class="custom-control-input">
                                <label class="custom-control-label" for="dia_miercoles">Miércoles</label>
                            </div>
                            <div class="col-md-2 custom-control custom-checkbox">
                                <input type="checkbox" name="dias[]" id="dia_jueves" value="Jueves" class="custom-control-input">
                                <label class="custom-control-label" for="dia_jueves">Jueves</label>
                            </div>
                            <div class="col-md-2 custom-control custom-checkbox">
                                <input type="checkbox" name="dias[]" id="dia_viernes" value="Viernes" class="custom-control-input">
                                <label class="custom-control-label" for="dia_viernes">Viernes</label>
                            </div>
                            <div class="col-md-2 custom-control custom-checkbox">
                                <input type="checkbox" name="dias[]" id="dia_sabado" value="Sabado" class="custom-control-input">
                                <label class="custom-control-label" for="dia_sabado">Sabado</label>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <p class="text-muted">Personal de pre-configuracion (no obligatorio)</p>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="conductor_id">Conductor</label>
                                <select name="conductor_id" id="conductor_id" class="form-control">
                                    <option value="">Seleccione Conductor</option>
                                    @foreach($conductores as $c)
                                        <option value="{{ $c->id }}">{{ $c->nombre_completo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ayudante1_id">Ayudante 1</label>
                                <select name="ayudante1_id" id="ayudante1_id" class="form-control">
                                    <option value="">Seleccione Ayudante</option>
                                    @foreach($ayudantes as $a)
                                        <option value="{{ $a->id }}">{{ $a->nombre_completo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ayudante2_id">Ayudante 2</label>
                                <select name="ayudante2_id" id="ayudante2_id" class="form-control">
                                    <option value="">Seleccione Ayudante</option>
                                    @foreach($ayudantes as $a)
                                        <option value="{{ $a->id }}">{{ $a->nombre_completo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12 text-left">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="estadoSwitch" checked> 
                                <label class="custom-control-label" for="estadoSwitch" id="estadoLabel">Activo</label>
                                <input type="hidden" name="estado" id="estado" value="1">
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
function aplicarExclusionAyudantes(selectCambiado, selectAExcluir) {
    const selectedId = $(selectCambiado).val();
    
    $(selectAExcluir).find('option').prop('disabled', false).css('display', '');
    
    if (selectedId) {
        const optionToExclude = $(selectAExcluir).find(`option[value="${selectedId}"]`);
        
        optionToExclude.prop('disabled', true).css('display', 'none');
        
        if ($(selectAExcluir).val() === selectedId) {
            $(selectAExcluir).val('').trigger('change'); 
        }
    }
}

$(function() {
    const tabla = $('#tablaGrupos').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
        }
    });
    
    function updateEstadoSwitch(estado) {
        const isChecked = estado == 1;
        $('#estadoSwitch').prop('checked', isChecked);
        $('#estado').val(estado);
        $('#estadoLabel').text(isChecked ? 'Activo' : 'Inactivo');
    }

    $('#estadoSwitch').on('change', function() {
        const estado = this.checked ? '1' : '0';
        $('#estado').val(estado);
        $('#estadoLabel').text(this.checked ? 'Activo' : 'Inactivo');
    });

    function resetFormulario() {
        $('#formGrupo')[0].reset();
        $('#grupo_id').val('');
        $('#tituloModalGrupo').text('Nuevo Grupo de Personal');
        
        updateEstadoSwitch(1); 
        
        $('#zona_id, #turno_id, #vehiculo_id, #conductor_id, #ayudante1_id, #ayudante2_id').val('').trigger('change');
        
        $('input[name="dias[]"]').prop('checked', false);

        aplicarExclusionAyudantes('#ayudante1_id', '#ayudante2_id'); 
        aplicarExclusionAyudantes('#ayudante2_id', '#ayudante1_id');
    }

    $('#btnNuevo').click(function() {
        resetFormulario();
        $('#modalGrupo').modal('show');
    });

    $(document).on('click', '.btnEditar', function() {
        const id = $(this).data('id');
        $('#tituloModalGrupo').text('Editar Grupo de Personal');
        
        $.get(`/grupospersonal/${id}/show`, function(res) {
            if (res.success) {
                const g = res.data;
                $('#grupo_id').val(g.id);
                $('#nombre').val(g.nombre);
                
                $('#zona_id').val(g.zona_id).trigger('change');
                $('#turno_id').val(g.turno_id).trigger('change');
                $('#vehiculo_id').val(g.vehiculo_id).trigger('change');
                
                updateEstadoSwitch(g.estado); 
                
                $('#conductor_id').val(g.conductor_id).trigger('change');
                $('#ayudante1_id').val(g.ayudante1_id).trigger('change');
                $('#ayudante2_id').val(g.ayudante2_id).trigger('change');

                $('input[name="dias[]"]').prop('checked', false);
                if (g.dias) {
                    const diasArray = g.dias.split(',').map(d => d.trim());
                    diasArray.forEach(dia => {
                        $(`input[name="dias[]"][value="${dia}"]`).prop('checked', true);
                    });
                }
                
                $('#modalGrupo').modal('show');
            }
        });
    });

    $('#formGrupo').submit(function(e) {
        e.preventDefault();
        const id = $('#grupo_id').val();
        const url = id ? `/grupospersonal/${id}/update` : '/grupospersonal/store';
        const method = id ? 'PUT' : 'POST';
        
        const diasSeleccionados = $('input[name="dias[]"]:checked').map(function() {
            return this.value;
        }).get().join(',');
        
        let formData = $(this).serializeArray();
        
        formData = formData.filter(item => item.name !== 'dias[]' && item.name !== 'estadoSwitch');
        formData.push({ name: 'dias', value: diasSeleccionados });

        if (method === 'PUT') {
            formData.push({ name: '_method', value: 'PUT' });
        }

        $.ajax({
            url: url
            , type: 'POST' 
            , data: formData
            , success: function(res) {
                if (res.success) {
                    $('#modalGrupo').modal('hide');
                    Swal.fire('Exito', res.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            }
            , error: function(xhr) {
                let msg = 'Error al guardar el grupo.';
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
            title: 'Esta seguro?'
            , text: 'Esta accion no se puede deshacer'
            , icon: 'warning'
            , showCancelButton: true
            , confirmButtonText: 'Si, eliminar'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/grupospersonal/${id}/destroy`
                    , type: 'POST'
                    , data: {
                        _token: '{{ csrf_token() }}'
                        , _method: 'DELETE'
                    }
                    , success: res => {
                        if (res.success) {
                            Swal.fire('Eliminado', res.message, 'success');
                            setTimeout(() => location.reload(), 1000);
                        }
                    }
                    , error: () => Swal.fire('Error', 'No se pudo eliminar el grupo', 'error')
                });
            }
        });
    });

    $('#ayudante1_id').on('change', function() {
        aplicarExclusionAyudantes('#ayudante1_id', '#ayudante2_id');
    });

    $('#ayudante2_id').on('change', function() {
        aplicarExclusionAyudantes('#ayudante2_id', '#ayudante1_id');
    });
    
    $('#modalGrupo').on('shown.bs.modal', function () {
        aplicarExclusionAyudantes('#ayudante1_id', '#ayudante2_id');
        aplicarExclusionAyudantes('#ayudante2_id', '#ayudante1_id');
    });
    
});
</script>
@endpush